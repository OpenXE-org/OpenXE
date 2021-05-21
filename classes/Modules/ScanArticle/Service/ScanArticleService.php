<?php

namespace Xentral\Modules\ScanArticle\Service;

use Xentral\Components\Http\Session\Session;
use Xentral\Modules\Article\Gateway\ArticleGateway;
use Xentral\Components\Http\Session\SessionHandler;
use Xentral\Modules\ScanArticle\Exception\ArticleNotFoundException;
use Xentral\Modules\ScanArticle\Exception\InvalidArgumentException;
use Xentral\Modules\ScanArticle\Wrapper\PriceWrapper;
use Xentral\Modules\ScanArticle\Wrapper\SavePositionWrapper;

class ScanArticleService
{
    /** @var ArticleGateway $articleGateway */
    private $articleGateway;

    /** @var Session $session */
    private $session;

    /** @var  PriceWrapper $priceWrapper */
    private $priceWrapper;

    /** @var SavePositionWrapper $savePositionWrapper */
    private $savePositionWrapper;

    private $segmentPrefix = 'scan_artikel_';
    private $key = 'article';

    /**
     * @param ArticleGateway      $articleGateway
     * @param Session             $session
     * @param PriceWrapper        $priceWrapper
     * @param SavePositionWrapper $savePositionWrapper
     */
    public function __construct(
        ArticleGateway $articleGateway,
        Session $session,
        PriceWrapper $priceWrapper,
        SavePositionWrapper $savePositionWrapper
    ) {
        $this->articleGateway = $articleGateway;
        $this->session = $session;
        $this->priceWrapper = $priceWrapper;
        $this->savePositionWrapper = $savePositionWrapper;
    }

    /**
     * @param string $modul
     * @param string $number
     * @param float $amount
     * @param int $documentId
     */
    public function writeArticleToSession($modul, $number, $amount, $documentId)
    {
        if ($modul === 'auftrag') {
            $articleData = $this->articleGateway->findScannableArticle($number);
            if(empty($articleData)) {
                $articleData = $this->articleGateway->findUniqueArticleBySerial($number);
            }
        } elseif ($modul === 'bestellung') {
            $articleData = $this->articleGateway->findScannableOrderPurchaseArticle($number,$documentId);
        } else {
            throw new InvalidArgumentException('modul can not be: ' . $modul);
        }

        if (empty($articleData)) {
            throw new ArticleNotFoundException('Article with number ' . $number . ' not found.');
        }

        $articleId = $articleData['id'];
        $articleName = $articleData['name_de'];

        $price=0;
        if ($modul === 'auftrag') {
            $price = $this->priceWrapper->getOrderSellingPrice($articleId, $amount, $documentId);
        } elseif ($modul === 'bestellung') {
            try{
                $price = $this->priceWrapper->getPurchaseOrderPurchasePrice($articleId, $amount, $documentId);
            }
            catch(InvalidArgumentException $e){
                $price = 0;
            }
        }

        $sessionArticles = $this->getAllArticleDataFromSession($modul);
        $index = count($sessionArticles) + 1;
        $sessionArticles[] = [
            'index'     => $index,
            'articleId' => $articleId,
            'number'    => $number,
            'name'      => $articleName,
            'amount'    => $amount,
            'price'     => $price,
        ];

        $this->session->setValue($this->segmentPrefix . $modul, $this->key, $sessionArticles);
        SessionHandler::commitSession($this->session);
    }

    public function getAllArticleDataFromSession($modul)
    {
        $data = $this->session->getValue($this->segmentPrefix . $modul, $this->key);
        if (!empty($data)) {
            return $data;
        }

        return [];
    }

    /**
     * @param string $modul
     */
    public function clearAllArticleDataInSession($modul)
    {
        $this->session->setValue($this->segmentPrefix . $modul, $this->key, []);
        SessionHandler::commitSession($this->session);
    }

    /**
     * @param string $modul
     * @param int    $documentId
     */
    public function saveSumedPositions($modul, $documentId)
    {
        $sessionArticlesFull = $this->getAllArticleDataFromSession($modul);
        $sessionArticles = [];
        $lastArticleId = 0;
        foreach($sessionArticlesFull as $sa) {
            if($sa['articleId'] !== $lastArticleId) {
                $sessionArticles[] = $sa;
                $lastArticleId = $sa['articleId'];
            }
            else {
                $sessionArticles[count($sessionArticles) - 1]['amount'] += $sa['amount'];
            }
        }
        if (!empty($sessionArticles)) {
            foreach ($sessionArticles as $sa) {
                if ($modul === 'auftrag') {
                    $this->savePositionWrapper->saveOrderPosition($sa['articleId'], $documentId, $sa['amount']);
                } elseif ($modul === 'bestellung') {
                    $this->savePositionWrapper->savePurchaseOrderPosition($sa['articleId'], $documentId, $sa['amount']);
                } else {
                    throw new InvalidArgumentException('modul can not be: ' . $modul);
                }
            }
            $this->clearAllArticleDataInSession($modul);
        }
    }

    /**
     * @param string $modul
     * @param int    $documentId
     */
    public function savePositions($modul, $documentId)
    {
        $sessionArticles = $this->getAllArticleDataFromSession($modul);
        if (!empty($sessionArticles)) {
            foreach ($sessionArticles as $sa) {
                if ($modul === 'auftrag') {
                    $this->savePositionWrapper->saveOrderPosition($sa['articleId'], $documentId, $sa['amount']);
                } elseif ($modul === 'bestellung') {
                    $this->savePositionWrapper->savePurchaseOrderPosition($sa['articleId'], $documentId, $sa['amount']);
                } else {
                    throw new InvalidArgumentException('modul can not be: ' . $modul);
                }
            }
            $this->clearAllArticleDataInSession($modul);
        }
    }
}
