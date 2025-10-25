"""
Email Parser Service

Handles parsing of .msg files using the extract_msg library.
Provides comprehensive email data extraction including metadata, content, and attachments.
"""

import json
import sys
import os
from datetime import datetime
from pathlib import Path
from typing import Dict, Any, Optional, Tuple

try:
    import extract_msg
except ImportError as e:
    print(f"Warning: extract_msg not installed: {e}")

from utils.logger import setup_logger

logger = setup_logger(__name__, 'email_parser.log')


class EmailParserService:
    """Service for parsing .msg email files."""
    
    def __init__(self):
        logger.info("Email Parser Service initialized")
    
    def parse_msg_file(self, file_path: str) -> Dict[str, Any]:
        """
        Parse a .msg file and extract all email data.
        
        Args:
            file_path: Path to the .msg file
        
        Returns:
            Dict containing parsed email data
        """
        try:
            logger.info(f"Parsing .msg file: {file_path}")
            
            if not os.path.exists(file_path):
                return {
                    'success': False,
                    'error': f'File not found: {file_path}'
                }
            
            # Parse the .msg file
            msg = extract_msg.Message(file_path)
            
            # Extract basic information
            email_data = {
                'success': True,
                'subject': self._safe_get(msg.subject, ''),
                'sender_name': '',
                'sender_email': '',
                'sent_date': self._safe_get(msg.date),
                'received_date': None,
                'html_content': self._safe_get(msg.htmlBody, ''),
                'text_content': self._safe_get(msg.body, ''),
                'recipients': [],
                'attachments': [],
                'headers': {},
                'message_id': self._safe_get(getattr(msg, 'messageId', ''), ''),
                'file_path': file_path,
                'file_size': os.path.getsize(file_path)
            }
            
            # Extract sender information
            sender_info = self._extract_sender_info(msg)
            email_data['sender_name'] = sender_info['name']
            email_data['sender_email'] = sender_info['email']
            
            # Extract recipients
            email_data['recipients'] = self._extract_recipients(msg)
            
            # Set received date (usually same as sent date for incoming emails)
            if email_data['sent_date']:
                email_data['received_date'] = email_data['sent_date']
            
            # Extract attachments
            email_data['attachments'] = self._extract_attachments(msg)
            
            # Extract headers
            email_data['headers'] = self._extract_headers(msg)
            
            logger.info(f"Successfully parsed email: {email_data['subject']}")
            
            return email_data
            
        except Exception as e:
            logger.error(f"Error parsing .msg file {file_path}: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'file_path': file_path
            }
    
    def _safe_get(self, value: Any, default: Any = None) -> Any:
        """Safely get value and convert to JSON-serializable format."""
        if value is None:
            return default
        
        if isinstance(value, str):
            return value
        elif isinstance(value, bytes):
            try:
                return value.decode('utf-8', errors='ignore')
            except:
                return str(value)
        elif isinstance(value, datetime):
            return value.isoformat()
        elif isinstance(value, (int, float, bool)):
            return value
        elif isinstance(value, (list, tuple)):
            return [self._safe_get(item) for item in value]
        elif isinstance(value, dict):
            return {str(k): self._safe_get(v) for k, v in value.items()}
        else:
            try:
                return str(value)
            except:
                return default
    
    def _extract_sender_info(self, msg) -> Dict[str, str]:
        """Extract sender name and email from message."""
        sender_fields = [
            'sender', 'from', 'senderEmail', 'senderEmailAddress', 'senderName',
            'from_', 'fromAddress', 'fromAddresses', 'fromEmail', 'fromEmailAddress',
            'fromName', 'fromDisplayName', 'fromDisplay', 'fromUser', 'fromUserEmail',
            'senderAddress', 'senderAddresses', 'senderDisplayName', 'senderDisplay',
            'senderUser', 'senderUserEmail', 'senderEmailAddresses', 'senderEmails'
        ]
        
        sender_info = None
        for field in sender_fields:
            try:
                if hasattr(msg, field):
                    value = getattr(msg, field)
                    if value:
                        sender_info = value
                        break
            except:
                continue
        
        if not sender_info:
            return {'name': '', 'email': ''}
        
        # Parse sender information
        name, email = self._extract_email_from_string(str(sender_info))
        return {'name': name or '', 'email': email or ''}
    
    def _extract_recipients(self, msg) -> list:
        """Extract recipient information from message."""
        recipient_fields = [
            'to', 'recipients', 'toRecipients', 'toAddress', 'toAddresses',
            'toEmail', 'toEmails', 'toEmailAddress', 'toEmailAddresses',
            'toName', 'toNames', 'toDisplayName', 'toDisplayNames',
            'recipient', 'recipientAddress', 'recipientAddresses',
            'recipientEmail', 'recipientEmails', 'recipientEmailAddress',
            'recipientEmailAddresses', 'recipientName', 'recipientNames'
        ]
        
        recipients = []
        for field in recipient_fields:
            try:
                if hasattr(msg, field):
                    value = getattr(msg, field)
                    if value:
                        if isinstance(value, str):
                            recipients.extend([r.strip() for r in value.split(',')])
                        elif isinstance(value, list):
                            recipients.extend([str(r).strip() for r in value])
                        elif hasattr(value, '__iter__') and not isinstance(value, (str, bytes)):
                            recipients.extend([str(r).strip() for r in value])
            except:
                continue
        
        # Remove duplicates and empty values
        recipients = list(set([r for r in recipients if r]))
        
        # Extract email addresses from recipient strings
        processed_recipients = []
        for recipient in recipients:
            name, email = self._extract_email_from_string(recipient)
            if email:
                processed_recipients.append(email)
            elif name:
                processed_recipients.append(name)
        
        return processed_recipients
    
    def _extract_email_from_string(self, text: str) -> Tuple[Optional[str], Optional[str]]:
        """Extract email address from string that might contain name and email."""
        if not text:
            return None, None
        
        text = str(text).strip()
        
        # Format: "Name <email@domain.com>"
        if '<' in text and '>' in text:
            try:
                email_part = text.split('<')[1].split('>')[0].strip()
                name_part = text.split('<')[0].strip()
                
                # Validate email
                if '@' in email_part and '.' in email_part.split('@')[1]:
                    return name_part if name_part else None, email_part
            except:
                pass
        
        # Format: "email@domain.com" or "Name email@domain.com"
        if '@' in text:
            parts = text.split()
            email_part = None
            name_parts = []
            
            for part in parts:
                if '@' in part and '.' in part.split('@')[1]:
                    email_part = part
                else:
                    name_parts.append(part)
            
            if email_part:
                name_part = ' '.join(name_parts) if name_parts else None
                return name_part, email_part
        
        # No valid email found
        return text if text else None, None
    
    def _extract_attachments(self, msg) -> list:
        """Extract attachment information from message."""
        attachments = []
        
        try:
            for attachment in msg.attachments:
                try:
                    attachment_data = {
                        'filename': self._safe_get(attachment.longFilename or attachment.shortFilename, 'Unknown'),
                        'content_type': self._safe_get(getattr(attachment, 'contentType', 'application/octet-stream'), 'application/octet-stream'),
                        'content_id': self._safe_get(getattr(attachment, 'contentId', ''), ''),
                        'is_inline': bool(getattr(attachment, 'contentId', None)),
                        'size': len(attachment.data) if attachment.data else 0,
                        'data': None
                    }
                    
                    # Only include data if it's not too large
                    if attachment.data and len(attachment.data) < 1000000:  # 1MB limit
                        try:
                            attachment_data['data'] = self._safe_get(attachment.data)
                        except:
                            attachment_data['data'] = None
                    
                    attachments.append(attachment_data)
                    
                except Exception as e:
                    logger.warning(f"Error processing attachment: {str(e)}")
                    # Add basic attachment info if detailed processing fails
                    attachments.append({
                        'filename': 'Unknown',
                        'content_type': 'application/octet-stream',
                        'content_id': '',
                        'is_inline': False,
                        'size': 0,
                        'data': None
                    })
        except Exception as e:
            logger.warning(f"Error extracting attachments: {str(e)}")
        
        return attachments
    
    def _extract_headers(self, msg) -> dict:
        """Extract email headers from message."""
        headers = {}
        
        try:
            if hasattr(msg, 'headers') and msg.headers:
                if isinstance(msg.headers, dict):
                    headers = {k: self._safe_get(v) for k, v in msg.headers.items()}
                elif isinstance(msg.headers, str):
                    # Parse headers manually
                    for line in msg.headers.split('\n'):
                        line = line.strip()
                        if ':' in line:
                            header_name, header_value = line.split(':', 1)
                            headers[header_name.strip()] = header_value.strip()
        except Exception as e:
            logger.warning(f"Error extracting headers: {str(e)}")
        
        return headers
    
    def test_parsing(self, file_path: str) -> Dict[str, Any]:
        """Test parsing on a specific file and return debug information."""
        try:
            logger.info(f"Testing parsing for: {file_path}")
            
            result = self.parse_msg_file(file_path)
            
            return {
                'success': True,
                'file_path': file_path,
                'file_exists': os.path.exists(file_path),
                'file_size': os.path.getsize(file_path) if os.path.exists(file_path) else 0,
                'parsed_data': result,
                'extract_msg_available': 'extract_msg' in sys.modules
            }
            
        except Exception as e:
            logger.error(f"Error in test parsing: {str(e)}")
            return {
                'success': False,
                'error': str(e),
                'file_path': file_path,
                'file_exists': os.path.exists(file_path)
            }
