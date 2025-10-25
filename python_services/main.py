#!/usr/bin/env python3
"""
Unified Python Services for Migration Manager

This service provides:
- PDF processing and conversion
- Email parsing (.msg files)
- Email analysis and categorization
- Document conversion (DOCX/DOC to PDF)
- Email rendering and enhancement

Author: Migration Manager Team
Version: 1.0.0
"""

import sys
import logging
from pathlib import Path
from typing import Dict, Any

from fastapi import FastAPI, UploadFile, File, HTTPException, Request
from fastapi.responses import JSONResponse, FileResponse
from fastapi.middleware.cors import CORSMiddleware
import uvicorn

# Add services to path
sys.path.insert(0, str(Path(__file__).parent))

from services.pdf_service import PDFService
from services.email_parser_service import EmailParserService
from services.email_analyzer_service import EmailAnalyzerService
from services.email_renderer_service import EmailRendererService
from utils.logger import setup_logger
from utils.validators import validate_file_type, validate_file_size

# Setup logging
logger = setup_logger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="Migration Manager Python Services",
    description="Unified Python services for PDF processing, email parsing, and document conversion",
    version="1.0.0"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure based on your needs
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize services
pdf_service = PDFService()
email_parser = EmailParserService()
email_analyzer = EmailAnalyzerService()
email_renderer = EmailRendererService()


# ============================================================================
# Health Check Endpoints
# ============================================================================

@app.get("/")
async def root():
    """Root endpoint with service information."""
    return {
        "service": "Migration Manager Python Services",
        "version": "1.0.0",
        "status": "running",
        "endpoints": {
            "pdf": "/pdf/*",
            "email": "/email/*",
            "health": "/health"
        }
    }


@app.get("/health")
async def health_check():
    """Health check endpoint."""
    return {
        "status": "healthy",
        "services": {
            "pdf_service": "ready",
            "email_parser": "ready",
            "email_analyzer": "ready",
            "email_renderer": "ready"
        }
    }


# ============================================================================
# PDF Service Endpoints
# ============================================================================

@app.post("/pdf/convert-to-images")
async def convert_pdf_to_images(file: UploadFile = File(...)):
    """Convert PDF pages to images."""
    try:
        logger.info(f"Converting PDF to images: {file.filename}")
        
        # Validate file
        if not validate_file_type(file.filename, ['.pdf']):
            raise HTTPException(status_code=400, detail="Invalid file type. Only PDF files are allowed.")
        
        # Read file content
        content = await file.read()
        
        # Convert to images
        result = pdf_service.convert_to_images(content, file.filename)
        
        return JSONResponse(content=result)
        
    except Exception as e:
        logger.error(f"Error converting PDF to images: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/pdf/merge")
async def merge_pdfs(files: list[UploadFile] = File(...)):
    """Merge multiple PDF files."""
    try:
        logger.info(f"Merging {len(files)} PDF files")
        
        # Validate files
        for file in files:
            if not validate_file_type(file.filename, ['.pdf']):
                raise HTTPException(status_code=400, detail=f"Invalid file type: {file.filename}")
        
        # Read file contents
        pdf_contents = [await file.read() for file in files]
        
        # Merge PDFs
        result = pdf_service.merge_pdfs(pdf_contents)
        
        return JSONResponse(content=result)
        
    except Exception as e:
        logger.error(f"Error merging PDFs: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


# ============================================================================
# Email Service Endpoints
# ============================================================================

@app.post("/email/parse")
async def parse_email(file: UploadFile = File(...)):
    """Parse .msg file and extract email data."""
    try:
        logger.info(f"Parsing email file: {file.filename}")
        
        # Validate file
        if not validate_file_type(file.filename, ['.msg']):
            raise HTTPException(status_code=400, detail="Invalid file type. Only .msg files are allowed.")
        
        # Save file temporarily
        temp_path = Path(f"temp/{file.filename}")
        temp_path.parent.mkdir(exist_ok=True)
        
        content = await file.read()
        temp_path.write_bytes(content)
        
        # Parse email
        result = email_parser.parse_msg_file(str(temp_path))
        
        # Clean up
        temp_path.unlink()
        
        return JSONResponse(content=result)
        
    except Exception as e:
        logger.error(f"Error parsing email: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/email/analyze")
async def analyze_email(request: Request):
    """Analyze email content for categorization, priority, sentiment, etc."""
    try:
        email_data = await request.json()
        logger.info(f"Analyzing email: {email_data.get('subject', 'No subject')}")
        
        # Analyze email
        result = email_analyzer.analyze_content(email_data)
        
        return JSONResponse(content=result)
        
    except Exception as e:
        logger.error(f"Error analyzing email: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/email/render")
async def render_email(request: Request):
    """Render email with enhanced HTML and styling."""
    try:
        email_data = await request.json()
        logger.info(f"Rendering email: {email_data.get('subject', 'No subject')}")
        
        # Render email
        result = email_renderer.render_email(email_data)
        
        return JSONResponse(content=result)
        
    except Exception as e:
        logger.error(f"Error rendering email: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/email/parse-analyze-render")
async def parse_analyze_render_email(file: UploadFile = File(...)):
    """
    Complete email processing pipeline:
    1. Parse .msg file
    2. Analyze content
    3. Render enhanced HTML
    """
    try:
        logger.info(f"Processing email file: {file.filename}")
        
        # Validate file
        if not validate_file_type(file.filename, ['.msg']):
            raise HTTPException(status_code=400, detail="Invalid file type. Only .msg files are allowed.")
        
        # Save file temporarily
        temp_path = Path(f"temp/{file.filename}")
        temp_path.parent.mkdir(exist_ok=True)
        
        content = await file.read()
        temp_path.write_bytes(content)
        
        # Step 1: Parse email
        parsed_data = email_parser.parse_msg_file(str(temp_path))
        
        if 'error' in parsed_data:
            return JSONResponse(content=parsed_data, status_code=500)
        
        # Step 2: Analyze email
        analysis = email_analyzer.analyze_content(parsed_data)
        
        # Step 3: Render email
        rendering = email_renderer.render_email(parsed_data)
        
        # Combine results
        result = {
            **parsed_data,
            'analysis': analysis,
            'rendering': rendering,
            'processing_status': 'success'
        }
        
        # Clean up
        temp_path.unlink()
        
        return JSONResponse(content=result)
        
    except Exception as e:
        logger.error(f"Error in email processing pipeline: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


# ============================================================================
# Error Handlers
# ============================================================================

@app.exception_handler(HTTPException)
async def http_exception_handler(request: Request, exc: HTTPException):
    """Handle HTTP exceptions."""
    logger.error(f"HTTP {exc.status_code}: {exc.detail}")
    return JSONResponse(
        status_code=exc.status_code,
        content={"error": exc.detail, "status_code": exc.status_code}
    )


@app.exception_handler(Exception)
async def general_exception_handler(request: Request, exc: Exception):
    """Handle general exceptions."""
    logger.error(f"Unexpected error: {str(exc)}", exc_info=True)
    return JSONResponse(
        status_code=500,
        content={"error": "Internal server error", "detail": str(exc)}
    )


# ============================================================================
# Main Entry Point
# ============================================================================

if __name__ == "__main__":
    import argparse
    
    parser = argparse.ArgumentParser(description="Migration Manager Python Services")
    parser.add_argument("--host", default="127.0.0.1", help="Host to bind to")
    parser.add_argument("--port", type=int, default=5000, help="Port to bind to")
    parser.add_argument("--reload", action="store_true", help="Enable auto-reload")
    
    args = parser.parse_args()
    
    logger.info(f"Starting Migration Manager Python Services on {args.host}:{args.port}")
    
    uvicorn.run(
        "main:app",
        host=args.host,
        port=args.port,
        reload=args.reload,
        log_level="info"
    )

