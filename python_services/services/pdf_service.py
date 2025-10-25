"""
PDF Processing Service

Handles PDF operations including:
- Converting PDF pages to images
- Merging multiple PDFs
- Extracting text from PDFs
- PDF metadata extraction
"""

import io
import base64
from typing import List, Dict, Any
from pathlib import Path

try:
    from PyPDF2 import PdfReader, PdfWriter
    from pdf2image import convert_from_bytes
    from PIL import Image
except ImportError as e:
    print(f"Warning: PDF dependencies not installed: {e}")

from utils.logger import setup_logger

logger = setup_logger(__name__, 'pdf_service.log')


class PDFService:
    """Service for PDF processing operations."""
    
    def __init__(self):
        self.max_dpi = 300
        self.default_dpi = 150
        logger.info("PDF Service initialized")
    
    def convert_to_images(
        self,
        pdf_content: bytes,
        filename: str,
        dpi: int = None,
        format: str = 'PNG'
    ) -> Dict[str, Any]:
        """
        Convert PDF pages to images.
        
        Args:
            pdf_content: PDF file content as bytes
            filename: Original filename
            dpi: Resolution (default: 150)
            format: Image format (PNG, JPEG)
        
        Returns:
            Dict with success status and image data
        """
        try:
            dpi = dpi or self.default_dpi
            dpi = min(dpi, self.max_dpi)  # Cap at max DPI
            
            logger.info(f"Converting PDF to images: {filename}, DPI: {dpi}")
            
            # Convert PDF to images
            images = convert_from_bytes(
                pdf_content,
                dpi=dpi,
                fmt=format.lower()
            )
            
            # Convert images to base64
            image_data = []
            for i, image in enumerate(images):
                buffer = io.BytesIO()
                image.save(buffer, format=format)
                img_base64 = base64.b64encode(buffer.getvalue()).decode('utf-8')
                
                image_data.append({
                    'page': i + 1,
                    'format': format,
                    'width': image.width,
                    'height': image.height,
                    'data': img_base64
                })
            
            logger.info(f"Successfully converted {len(images)} pages to images")
            
            return {
                'success': True,
                'filename': filename,
                'total_pages': len(images),
                'dpi': dpi,
                'format': format,
                'images': image_data
            }
            
        except Exception as e:
            logger.error(f"Error converting PDF to images: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'filename': filename
            }
    
    def merge_pdfs(self, pdf_contents: List[bytes]) -> Dict[str, Any]:
        """
        Merge multiple PDF files into one.
        
        Args:
            pdf_contents: List of PDF file contents as bytes
        
        Returns:
            Dict with success status and merged PDF data
        """
        try:
            logger.info(f"Merging {len(pdf_contents)} PDF files")
            
            writer = PdfWriter()
            
            # Add all pages from all PDFs
            for pdf_bytes in pdf_contents:
                reader = PdfReader(io.BytesIO(pdf_bytes))
                for page in reader.pages:
                    writer.add_page(page)
            
            # Write to bytes
            output_buffer = io.BytesIO()
            writer.write(output_buffer)
            merged_pdf = output_buffer.getvalue()
            
            # Convert to base64 for JSON response
            pdf_base64 = base64.b64encode(merged_pdf).decode('utf-8')
            
            logger.info(f"Successfully merged {len(pdf_contents)} PDFs")
            
            return {
                'success': True,
                'total_files': len(pdf_contents),
                'total_pages': len(writer.pages),
                'data': pdf_base64
            }
            
        except Exception as e:
            logger.error(f"Error merging PDFs: {str(e)}")
            return {
                'success': False,
                'error': str(e)
            }
    
    def extract_text(self, pdf_content: bytes) -> Dict[str, Any]:
        """
        Extract text from PDF.
        
        Args:
            pdf_content: PDF file content as bytes
        
        Returns:
            Dict with success status and extracted text
        """
        try:
            logger.info("Extracting text from PDF")
            
            reader = PdfReader(io.BytesIO(pdf_content))
            
            text_by_page = []
            for i, page in enumerate(reader.pages):
                text = page.extract_text()
                text_by_page.append({
                    'page': i + 1,
                    'text': text
                })
            
            all_text = '\n\n'.join([p['text'] for p in text_by_page])
            
            logger.info(f"Extracted text from {len(reader.pages)} pages")
            
            return {
                'success': True,
                'total_pages': len(reader.pages),
                'pages': text_by_page,
                'full_text': all_text
            }
            
        except Exception as e:
            logger.error(f"Error extracting text from PDF: {str(e)}")
            return {
                'success': False,
                'error': str(e)
            }

