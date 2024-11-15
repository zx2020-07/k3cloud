<?php

namespace Glitter\K3Cloud;


use Glitter\K3Cloud\Consts\ApiAuthTypeConst;
use Glitter\K3Cloud\Consts\ApiPathConst;
use Glitter\K3Cloud\Core\WebApiClient;
use Glitter\K3Cloud\Exceptions\AuthorizationException;
use Glitter\K3Cloud\Exceptions\HttpException;
use GuzzleHttp\Exception\GuzzleException;

class K3CloudApiSdk
{
    // 金蝶域名或者IP地址;/K3Cloud/
    public $hostUrl = '';

    public $config = [];

    public $webApiClient;

    public function __construct($config)
    {
        $this->hostUrl = rtrim($config['host_url'], "/") . "/";

        $this->config = $config;

        $this->webApiClient = new WebApiClient();

        $type = $this->config['auth_type'] ?? 1;


        switch ($type) {
            case ApiAuthTypeConst::USER_ID_PASSWORD:
                $this->loginForPassword();
                break;
            case ApiAuthTypeConst::APP_ID_SECRET:
                $this->loginForSecret();
                break;
            case ApiAuthTypeConst::API_SIGNATURE:
            default:
                break;
        }
    }

    /**
     * 登录: 用户名+密码
     * @return void
     * @throws AuthorizationException
     * @throws GuzzleException
     * @throws HttpException
     */
    public function loginForPassword()
    {
        $url = $this->hostUrl . ApiPathConst::LOGIN_API;

        $postData = [
            'acctid' => $this->config['acct_id'],         // 账户ID
            'username' => $this->config['username'],      // 用户名
            'password' => $this->config['password'],      // 密码
            'lcid' => $this->config['lcid'] ?? 2052,      // 语言
        ];
        $ret = $this->webApiClient->execute($url, [], $postData, 'array');
        if (!$ret['IsSuccessByAPI']) {
            throw new AuthorizationException("登录失败", '401');
        }
    }

    /**
     * 登录: 第三方授权应用ID+应用密钥
     * @return void
     * @throws AuthorizationException
     * @throws GuzzleException
     * @throws HttpException
     */
    public function loginForSecret()
    {
        $url = $this->hostUrl . ApiPathConst::LOGIN_API_APP_SECRET;

        $postData = [
            'acctid' => $this->config['acct_id'],         // 账户ID
            'username' => $this->config['username'],      // 用户名
            'appid' => $this->config['appid'],            // 应用ID
            'appsecret' => $this->config['appsecret'],    // 应用密钥
            'lcid' => $this->config['lcid'] ?? 2052,      // 语言
        ];

        $ret = $this->webApiClient->execute($url, [], $postData, 'array');

        if (!$ret['IsSuccessByAPI']) {
            throw new AuthorizationException("登录失败", '401');
        }
    }

    /**
     * 登录: 签名
     * @param $url
     * @return array
     */
    public function getHeaders($url): array
    {
        $headers = [];
        if ($this->config['auth_type'] == ApiAuthTypeConst::API_SIGNATURE) {
            $headers = $this->webApiClient->buildHeader($url, $this->config);
        }
        return $headers;
    }

    /**
     * 详情
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function view($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::VIEW_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     *  单据查询
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws HttpException
     * @throws GuzzleException
     */
    public function executeBillQuery($data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::EXECUTEBILLQUERY_API;
        $postData = [
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 元数据查询
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function queryBusinessInfo($data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::QUERYBUSINESSINFO_API;
        $postData = [
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 获取数据中心列表
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function getDataCenterList(string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::GETDATACENTERLIST_API;
        return $this->webApiClient->execute($url, $this->getHeaders($url), [], $format);
    }

    /**
     * 保存
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function save($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::SAVE_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 批量保存
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function batchSave($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::BATCHSAVE_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 审核
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function audit($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::AUDIT_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 反审核
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function unAudit($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::UNAUDIT_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 提交
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function submit($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::SUBMIT_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 操作
     * @param $formId
     * @param $opNumber
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function operation($formId, $opNumber, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::EXCUTEOPERATION_API;
        $postData = [
            'formid' => $formId,
            'opNumber' => $opNumber,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 下推
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function push($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::PUSH_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 暂存
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function draft($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::DRAFT_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 删除
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function delete($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::DELETE_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 分配
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function allocate($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::ALLOCATE_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 弹性域保存
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function flexSave($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::FLEXSAVE_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 发送消息
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function sendMsg($data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::SENDMSG_API;
        $postData = [
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 分组保存
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function groupSave($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::GROUPSAVE_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 拆单
     * @param $formId
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function disassembly($formId, $data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::DISASSEMBLY_API;
        $postData = [
            'formid' => $formId,
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 工作流审批
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function workflowAudit($data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::WORKFLOWAUDIT_API;
        $postData = [
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 查询分组信息
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function queryGroupInfo($data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::QUERYGROUPINFO_API;
        $postData = [
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }

    /**
     * 分组删除
     * @param $data
     * @param string $format
     * @return mixed|string|void
     * @throws GuzzleException
     * @throws HttpException
     */
    public function groupDelete($data, string $format = 'string')
    {
        $url = $this->hostUrl . ApiPathConst::GROUPDELETE_API;
        $postData = [
            'data' => $data
        ];
        return $this->webApiClient->execute($url, $this->getHeaders($url), $postData, $format);
    }
}
