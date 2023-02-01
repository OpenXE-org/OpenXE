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

    /**
     * @param MailMessagePartInterface $part
     *
     * @throws InvalidArgumentException
     *
     * @return MailAttachmentData
     */
    public static function fromMailMessagePart(MailMessagePartInterface $part): MailAttachmentData
    {

        $isInline = false;             

        $encodingHeader = $part->getHeader('content-transfer-encoding');
        if ($encodingHeader === null) {
         // Assume this is no error (?)   throw new InvalidArgumentException('missing header: "Content-Transfer-Encoding"');
            $encoding = '';
        } else {
            $encoding = $encodingHeader->getValue();
        }
        $dispositionHeader = $part->getHeader('content-disposition');
        if ($dispositionHeader === null) {
            throw new InvalidArgumentException('missing header: "Content-Disposition"');
        }
        $disposition = $dispositionHeader->getValue();      

        /*        
        Content-Disposition: inline
        Content-Disposition: attachment
        Content-Disposition: attachment; filename="filename.jpg"

        This is not correctly implemented -> only the first string is evaluated
        Content-Disposition: attachment; filename*0="filename_that_is_"
        Content-Disposition: attachment; filename*1="very_long.jpg"

        */

        if (preg_match('/(.+);\s*filename(?:\*[0-9]){0,1}="([^"]+)".*$/m', $disposition, $matches)) {
            $isInline = strtolower($matches[1]) === 'inline';
            $filename = $matches[2];                 
        }
        else if ($disposition == 'attachment') {
            // Filename is given in Content-Type e.g. 
            /* Content-Type: application/pdf; name="Filename.pdf" 
               Content-Transfer-Encoding: base64
               Content-Disposition: attachment
            */

            $contenttypeHeader = $part->getHeader('content-type');
            if ($contenttypeHeader === null) {
                throw new InvalidArgumentException('missing header: "Content-Type"');
            }
            $contenttype = $contenttypeHeader->getValue();

            if (preg_match('/(.+);\s*name(?:\*[0-9]){0,1}="([^"]+)".*$/m', $contenttype, $matches)) {
                $isInline = strtolower($matches[1]) === 'inline';         
                $filename = $matches[2];                   
            } else {
                throw new InvalidArgumentException(
                    sprintf('missing filename in header value "Content-Type" = "%s"', $contenttype)
                );
            }
        }
        else if ($disposition == 'inline') {
            $isInline = true;
            $filename = "OpenXE_file.inline"; 
        }
        else if (strpos($disposition,'attachment;\n') == 0) { // No filename, check for content type message/rfc822
            
            $contenttypeHeader = $part->getHeader('content-type');
            if ($contenttypeHeader === null) {
                throw new InvalidArgumentException('missing header: "Content-Type"');
            }
            $contenttype = $contenttypeHeader->getValue();

            if ($contenttype == 'message/rfc822') {   
                $filename = 'ForwardedMessage.eml';               
            } else {
              /*  throw new InvalidArgumentException(
                    sprintf('unexpected header value "Content-Disposition" = "%s"', $disposition)
                );*/
                $filename = "OpenXE_file.unknown"; 
            }
        }
        else {
            throw new InvalidArgumentException(
                sprintf('unexpected header value "Content-Disposition" = "%s", not message/rfc822', $disposition)
            );
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

        $content = $part->getContent();
        if ($content === null) { // This should not be
            throw new InvalidArgumentException(
                sprintf('content is null "%s"', substr(print_r($part,true),1000))
            );
        }

        return new self(
            $filename,
            $content,
            $part->getContentType(),
            $encoding,
            $isInline,
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
