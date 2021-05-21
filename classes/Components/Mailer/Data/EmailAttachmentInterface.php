<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Data;

use SplFileInfo;

interface EmailAttachmentInterface
{
    /** @var string DISPOSITION_ATTACHMENT */
    const DISPOSITION_ATTACHMENT = 'attachment';

    /** @var string DISPOSITION_INLINE */
    const DISPOSITION_INLINE = 'inline';

    /** @var string ENCODING_7BIT */
    const ENCODING_7BIT = '7bit';

    /** @var string ENCODING_8BIT */
    const ENCODING_8BIT = '8bit';

    /** @var string ENCODING_BASE64 */
    const ENCODING_BASE64 = 'base64';

    /** @var string ENCODING_BINARY */
    const ENCODING_BINARY = 'binary';

    /** @var string ENCODING_QUOTED_PRINTABLE */
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

    /**
     * @return string
     */
    public function getPath():string;

    /**
     * @return string
     */
    public function getType():string;

    /**
     * @return string
     */
    public function getName():string;

    /**
     * @return string
     */
    public function getEncoding():string;

    /**
     * @return string
     */
    public function getDisposition():string;
}
