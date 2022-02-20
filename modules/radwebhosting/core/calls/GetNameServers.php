<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of GetNameServers
 *
 * @author inbs
 */
class GetNameServers extends Call
{
    public $action = "domains/:domain/nameservers";
    
    public $type = parent::TYPE_GET;
}