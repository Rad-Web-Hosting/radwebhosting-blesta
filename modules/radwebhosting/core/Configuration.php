<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core;

/**
 * Description of Configuration
 *
 * @author Paweł Złamaniec <pawel.zl@modulesgarden.com>
 */
class Configuration
{
    const FIELD_USERNAME     = "Username";
    const FIELD_API_KEY      = "ApiKey";
    const FIELD_API_ENDPOINT = "ApiEndpoint";

    /**
     * @var mixed
     */
    protected $configuration;

    /**
     * Get configuration values and create params
     *
     * @param $meta
     * @return Configuration
     * @throws \Exception
     */
    public static function create($meta)
    {
        return new Configuration(
        [
            self::FIELD_USERNAME        => $meta->user,
            self::FIELD_API_KEY         => $meta->key,
            self::FIELD_API_ENDPOINT    => $meta->endpoint
        ]);
    }

    /**
     * Create Configuration
     *
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        $this->configuration = $params;
    }

    /**
     * Get values from configuration array
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->configuration[$name];
    }

    /**
     * Create authorization headers
     *
     * @return array
     */
    public function getAuthHeaders()
    {
        $time = gmdate("y-m-d H");
        $token = base64_encode(hash_hmac("sha256", $this->ApiKey, "{$this->Username}:$time"));

        return
        [
            "username"  => $this->Username,
            "token"     => $token
        ];
    }
}