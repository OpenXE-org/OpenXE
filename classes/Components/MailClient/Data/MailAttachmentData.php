<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

use Xentral\Components\MailClient\Exception\InvalidArgumentException;

class MailAttachmentData implements MailAttachmentInterface
{
    /** @var string $filename */
    private $filename;

    /** @var string $content */
    private $content;

    /** @var string $contentType */
    private $contentType;

    /** @var string $encoding */
    private $encoding;

    /** @var bool $isInlineAttachment*/
    private $isInlineAttachment;

    /** @var string|null $cid */
    private $cid;

    /**
     * @param string      $filename
     * @param string      $content
     * @param string      $contentType
     * @param string      $encoding
     * @param bool        $isInlineAttachment
     * @param string|null $cid
     */
    public function __construct(
        string $filename,
        string $content,
        string $contentType,
        string $encoding,
        bool $isInlineAttachment = false,
        string $cid = null
    )
    {
        $this->filename = $filename;
        $this->content = $content;
        $this->contentType = $contentType;
        $this->encoding = $encoding;
        $this->isInlineAttachment = $isInlineAttachment;
        $this->cid = $cid;
    }


    /*
        Check the type of Attachment
        Possible results: application/octet-stream, attachment, inline    
    */
    public static function getAttachmentPartType(MailMessagePartInterface $part): ?string {

        if (!$part->isMultipart()) {
            $header = $part->getHeader('content-disposition');
            if ($header !== null) {
                $split = explode(';', $header->getValue());
                if ($split[0] === 'attachment') {
                    return ('attachment');
                } else if ($split[0] === 'inline') {
                    return ('inline');
                }
            } else {    
                // No disposition given (that is a bad thing)
                // Check for application/octet-stream
                $content_type = $part->getContentType();
                if ($content_type == 'application/octet-stream') {
                    return('application/octet-stream');
                }                
                // Check for application/pdf
                $content_type = $part->getContentType();
                if ($content_type == 'application/pdf') {
                    return('application/pdf');
                }
                // Check for Content-id
                $contentIdHeader = $part->getHeader('content-id');                                              
                if ($contentIdHeader !== null) {
                    return ('inline');
                }               
            }
        }

        return(null);
    }

    /**
     * @param MailMessagePartInterface $part
     * @throws InvalidArgumentException
     *
     * @return MailAttachmentData
     */
    public static function fromMailMessagePart(MailMessagePartInterface $part): MailAttachmentData
    {

        $attachmenttype = MailAttachmentData::getAttachmentPartType($part);

        if ($attachmenttype == null) {
            throw new InvalidArgumentException('object is no attachment');
        }

        $disposition = $part->getHeaderValue('content-disposition');
        if ($disposition == null) {
            $disposition = '';
        }

        $disposition = str_replace(["\n\r", "\n", "\r"], '', $disposition);

  //      file_put_contents('debug.txt',date("HH:mm:ss")."\nDispo: ".$disposition); // FILE_APPEND

        // Determine filename
        /*        
        Content-Disposition: inline
        Content-Disposition: attachment
        Content-Disposition: attachment; filename="filename.jpg"

        This is not correctly implemented -> only the first string is evaluated
        Content-Disposition: attachment; filename*0="filename_that_is_"
        Content-Disposition: attachment; filename*1="very_long.jpg"
        */
        $filename = 'OpenXE_file.unknown';
        if (preg_match('/(.+);\s*filename(?:\*[0-9]){0,1}="*([^"]+)"*.*$/m', $disposition, $matches)) { // Filename in disposition
            $filename = $matches[2];                 
        } else {
            $contenttype = $part->getHeaderValue('content-type');

            $contenttype = str_replace(["\n\r", "\n", "\r"], '', $contenttype);

//            file_put_contents('debug.txt',date("HH:mm:ss")."\nConttype: ".$contenttype,FILE_APPEND); // FILE_APPEND

            if (preg_match('/(.+);\s*name(?:\*[0-9]){0,1}="*([^"]+)"*.*$/m', $contenttype, $matches)) { // Name in content-type
                $filename = $matches[2];                   
            } else if ($contenttype == 'message/rfc822') { // RFC822 message  
                $filename = 'ForwardedMessage.eml';                     
            }
        }

        $encodingHeader = $part->getHeader('content-transfer-encoding');
        if ($encodingHeader === null) {
            $content_transfer_encoding = '';
        } else {
            $content_transfer_encoding = $encodingHeader->getValue();
        }

        // Thunderbird UTF URL-Format
        $UTF_pos = strpos($filename,'UTF-8\'\'');
        if ($UTF_pos !== false) {            
            $wasUTF = "JA";
            $filename = substr($filename,$UTF_pos);
            $filename = rawurldecode($filename);
        }
   
        $cid = null;
        $contentIdHeader = $part->getHeader('content-id');
        if ($contentIdHeader !== null) {
            $cid = $contentIdHeader->getValue();
            if (preg_match('/[<]?([^<>]+)[>]?$/', $cid, $cidMatches)) {
                $cid = $cidMatches[1];
            }
        }
        if ($attachmenttype == 'inline' && $cid != null) {
            $filename = "cid:".$cid;
        }

        $content = $part->getContent();
        if ($content === null) { // This should not be
//            file_put_contents('debug.txt',date("HH:mm:ss")."\n".print_r($part,true)); // FILE_APPEND
            throw new InvalidArgumentException(
                sprintf('content is null "%s"', substr(print_r($part,true),0,1000))
            );
        }

        return new self(
            $filename,
            $content,
            $part->getContentType(),
            $content_transfer_encoding,
            $attachmenttype == 'inline',
            $cid
        );
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        switch ($this->encoding) {
            case 'base64':
                return base64_decode($this->content);

            default:
                return $this->content;
        }
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getTransferEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return bool
     */
    public function isInlineAttachment(): bool
    {
        return $this->isInlineAttachment;
    }

    /**
     * @return string|null
     */
    public function getCid(): ?string
    {
        return $this->cid;
    }
}
