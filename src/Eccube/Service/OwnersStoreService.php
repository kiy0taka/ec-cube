<?php


namespace Eccube\Service;


use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Symfony\Component\HttpFoundation\Request;

class OwnersStoreService
{

    private $endpoint;

    /**
     * @var BaseInfo $BaseInfo
     */
    private $BaseInfo;

    function __construct(Application $app)
    {
        $this->endpoint = $app['config']['owners_store_url'];
        $this->BaseInfo = $app['eccube.repository.base_info']->get();
    }

    public function doList(Request $request)
    {
        return $this->getRequestApi($request, 'method=list');
    }

    public function doCommit(Request $request, $id, $version, $message = null)
    {
        if (is_null($message)) {
            return $this->getRequestApi($request, 'method=commit&product_id='.$id.'&status=1&version='.$version);
        }
        return $this->getRequestApi($request, '?method=commit&product_id='.$id.'&status=0&version='.$version.'&message='.urlencode($message));
    }

    private function getRequestApi(Request $request, $url)
    {
        $curl = curl_init($this->endpoint.'?'.$url);

        $options = array(           // オプション配列
            //HEADER
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.base64_encode($this->BaseInfo->getAuthenticationKey()),
                'x-eccube-store-url: '.base64_encode($request->getSchemeAndHttpHost().$request->getBasePath()),
                'x-eccube-store-version: '.base64_encode(Constant::VERSION),
            ),
            CURLOPT_HTTPGET => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_CAINFO => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath(),
        );

        curl_setopt_array($curl, $options); /// オプション値を設定
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        log_info('http get_info', $info);

        return array($result, $info);
    }
}