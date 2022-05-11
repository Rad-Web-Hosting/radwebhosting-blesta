<?php
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Loader as ModuleLoader;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;

/**
 * radwebhosting
 */
class radwebhosting extends Module
{
    /**
     * @var string The version of this module
     */
    private static $version = '1.0.0';

    /**
     * @var string The authors of this module
     */
    private static $authors =
    [
        [
            'name' => "RadWebHosting", 'url' => "https://radwebhosting.com/"
        ]
    ];

    /**
     * Initializes the module
     */
    public function __construct()
    {
        //Register loader
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . "Loader.php";
        new ModuleLoader(__DIR__);

        // Load components required by this module
        Loader::loadComponents($this, ['Input']);

        // Load the language required by this module
        Language::loadLang('radwebhosting', null, dirname(__FILE__) . DS . 'language' . DS);
        Configure::load('radwebhosting', dirname(__FILE__) . DS . 'config' . DS);
    }

    /**
     * Returns the name of this module
     *
     * @return string
     */
    public function getName()
    {
        return "RadWebHosting";
    }

    /**
     * Returns the version of this module
     *
     * @return string
     */
    public function getVersion()
    {
        return self::$version;
    }

    /**
     * Returns the name and URL for the authors of this module
     *
     * @return array A numerically indexed array that contains an array with key/value pairs for
     *  'name' and 'url', representing the name and URL of the authors of this module
     */
    public function getAuthors()
    {
        return self::$authors;
    }

    /**
     * Returns the value used to identify a particular service
     *
     * @param stdClass $service A stdClass object representing the service
     * @return string A value used to identify this service amongst other similar services
     */
    public function getServiceName($service)
    {
        foreach ($service->fields as $field)
        {
            if ($field->key == 'domain')
            {
                return $field->value;
            }
        }

        return null;
    }

    /**
     * Returns a noun used to refer to a module row (e.g. "Server", "VPS", "Reseller Account", etc.)
     *
     * @return string The noun used to refer to a module row
     */
    public function moduleRowName()
    {
        return Language::_('radwebhosting.module_row', true);
    }

    /**
     * Returns a noun used to refer to a module row in plural form (e.g. "Servers", "VPSs", "Reseller Accounts", etc.)
     *
     * @return string The noun used to refer to a module row in plural form
     */
    public function moduleRowNamePlural()
    {
        return Language::_('radwebhosting.module_row_plural', true);
    }

    /**
     * Returns a noun used to refer to a module group (e.g. "Server Group", "Cloud", etc.)
     *
     * @return string The noun used to refer to a module group
     */
    public function moduleGroupName()
    {
        return null;
    }

    /**
     * Returns the key used to identify the primary field from the set of module row meta fields.
     * This value can be any of the module row meta fields.
     *
     * @return string The key used to identify the primary field from the set of module row meta fields
     */
    public function moduleRowMetaKey()
    {
        return 'user';
    }

    /**
     * Returns the value used to identify a particular package service which has
     * not yet been made into a service. This may be used to uniquely identify
     * an uncreated services of the same package (i.e. in an order form checkout)
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @return string The value used to identify this package service
     * @see Module::getServiceName()
     */
    public function getPackageServiceName($packages, array $vars = null)
    {
        if (isset($vars['domain'])) {
            return $vars['domain'];
        }
        return null;
    }

    /**
     * Attempts to validate service info. This is the top-level error checking method. Sets Input errors on failure.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @return bool True if the service validates, false otherwise. Sets Input errors when false.
     */
    public function validateService($package, array $vars = null)
    {
        $this->Input->setRules($this->getServiceRules($vars));
        return $this->Input->validates($vars);
    }

    /**
     * Attempts to validate an existing service against a set of service info updates. Sets Input errors on failure.
     *
     * @param stdClass $service A stdClass object representing the service to validate for editing
     * @param array $vars An array of user-supplied info to satisfy the request
     * @return bool True if the service update validates or false otherwise. Sets Input errors when false.
     */
    public function validateServiceEdit($service, array $vars = null)
    {
        $this->Input->setRules($this->getServiceRules($vars, true));
        return $this->Input->validates($vars);
    }

    /**
     * Returns the rule set for adding/editing a service
     *
     * @param array $vars A list of input vars
     * @param bool $edit True to get the edit rules, false for the add rules
     * @return array Service rules
     */
    private function getServiceRules(array $vars = null, $edit = false)
    {


        return $rules;
    }
    
    private function getAdditionalDomainFields($vars, $tld)
    {
        $domainFields = [];          
        foreach(Configure::get('radwebhosting.domain_fields'.$tld) as $key => $value)
        {
            $fieldKey = str_replace(' ', '_', $key);
            if(isset($vars[$fieldKey]))
            {
                $domainFields[$key] = $vars[$fieldKey];
            }
        }
        
        return $domainFields;
    }

    /**
     * Adds the service to the remote server. Sets Input errors on failure,
     * preventing the service from being added.
     *
     * @param $package
     * @param array $vars
     * @param array $parent_package
     * @param array $parent_service
     * @param string $status
     * @return array
     * @throws Exception
     */
    public function addService($package, array $vars=null, $parent_package=null, $parent_service=null, $status="pending")
    {
        $tld = null;
        $input_fields = [];

        if ($package->meta->type == 'domain')
        {
            if (array_key_exists('EPPCode', $vars))
            {
                $input_fields = array_merge(Configure::get('radwebhosting.transfer_fields'), ['Years' => true]);
            }
            else
            {
                if (isset($vars['domain']))
                {
                    $tld = $this->getTld($vars['domain']);
                }

                $domainFields   = Configure::get('radwebhosting.domain_fields');
                $whoisFields    = Configure::get('radwebhosting.whois_fields');
                $tldFields      = Configure::get('radwebhosting.domain_fields' . $tld);
                $input_fields   = array_merge([], $domainFields, $whoisFields, $tldFields ?: [], ['Years' => true, 'Nameservers' => true]);
            }
        }

        if (isset($vars['use_module']) && $vars['use_module'] == 'true')
        {
            if ($package->meta->type == 'domain')
            {
                $vars['Years'] = 1;
                foreach ($package->pricing as $pricing)
                {
                    if ($pricing->id == $vars['pricing_id'])
                    {
                        $vars['Years'] = $pricing->term;
                        break;
                    }
                }

                // Set all whois info from client ($vars['client_id'])
                if (!isset($this->Clients))
                {
                    Loader::loadModels($this, ['Clients']);
                }

                if (!isset($this->Contacts))
                {
                    Loader::loadModels($this, ['Contacts']);
                }

                $client = $this->Clients->get($vars['client_id']);
                if ($client)
                {
                    $contact_numbers = $this->Contacts->getNumbers($client->contact_id);
                }

                //Set contact post fields
                foreach($whoisFields as $contact => $fields)
                {
                    foreach($fields as $key => $value)
                    {
                        switch($key)
                        {
                            case "fistname":
                                $vars[$contact][$key] = $client->first_name;
                                break;
                            case "lastname":
                                $vars[$contact][$key] = $client->last_name;
                                break;
                            case "fullname":
                                $vars[$contact][$key] = $client->first_name ." ". $client->last_name;
                                break;
                            case "zipcode":
                                $vars[$contact]['postcode'] = $client->zip;
                                break;
                            case "phone":
                                $vars[$contact][$key] = $this->formatPhone(isset($contact_numbers[0]) ? $contact_numbers[0]->number : null, $client->country);
                                break;

                            default:
                                $vars[$contact][$key] = $client->{$key};
                        }

                    }
                }

                for ($i=1; $i<=5; $i++)
                {
                    $key = 'ns' . $i;
                    $vars["nameservers"][$key] = $vars[$key];
                    unset($vars[$key]);
                }

                $postfields =
                [
                    "domain"        => $vars["domain"],
                    "regperiod"     => $vars["Years"],
                    "domainfields"  => base64_encode(serialize($this->getAdditionalDomainFields($vars, $tld))),
                    "addons"        => [],
                    "nameservers"   => $vars["nameservers"],
                    "contacts"      =>
                    [
                        "tech"          => $vars["tech"],
                        "registrant"    => $vars["registrant"],
                    ]
                ];

                // Handle transfer
                if (isset($vars['transfer']) || isset($vars['EPPCode']))
                {
                    $postfields["eppcode"] = $vars['EPPCode'];
                    $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
                    $result = (new Calls\TransferDomain($config, $postfields))->process();
                }
                else
                {
                    $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
                    $result = (new Calls\RegisterDomain($config, $postfields))->process();
                }

                if (isset($result["error"]))
                {
                    $this->Input->setErrors(['errors' => [$result["error"]]]);
                    return;
                }

                return [['key' => 'domain', 'value' => $vars['domain'], 'encrypted' => 0]];
            }
        }

        $meta = [];
        $fields = array_intersect_key($vars, $input_fields);
        foreach ($fields as $key => $value) {
            $meta[] = [
                'key' => $key,
                'value' => $value,
                'encrypted' => 0
            ];
        }

        return $meta;
    }

    /**
     * Edits the service on the remote server. Sets Input errors on failure,
     * preventing the service from being edited.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $vars An array of user supplied info to satisfy the request
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being edited (if the current service is an addon service)
     * @return array A numerically indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editService($package, $service, array $vars = [], $parent_package = null, $parent_service = null)
    {
        return null; // All this handled by admin/client tabs instead
    }

    /**
     * Cancels the service on the remote server. Sets Input errors on failure,
     * preventing the service from being canceled.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being canceled (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function cancelService($package, $service, $parent_package = null, $parent_service = null)
    {
        return null; // Nothing to do
    }

    /**
     * Suspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being suspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being suspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function suspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        return null; // Nothing to do
    }

    /**
     * Unsuspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being unsuspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being unsuspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function unsuspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        return null; // Nothing to do
    }

    /**
     * Allows the module to perform an action when the service is ready to renew.
     * Sets Input errors on failure, preventing the service from renewing.
     *
     * @param $package
     * @param $service
     * @param null $parent_package
     * @param null $parent_service
     * @return null
     * @throws Exception
     */
    public function renewService($package, $service, $parent_package = null, $parent_service = null)
    {
        // Renew domain
        if ($package->meta->type == 'domain')
        {
            $fields = $this->serviceFieldsToObject($service->fields);

            $postfields =
            [
                'domain'    => $fields->domain,
                'regperiod' => 1,
                'addons'    => []
            ];

            foreach ($package->pricing as $pricing)
            {
                if ($pricing->id == $service->pricing_id)
                {
                    $postfields['regperiod'] = $pricing->term;
                    break;
                }
            }

            $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
            $result = (new Calls\RenewDomain($config, $postfields))->process();

            if ($result["error"])
            {
                $this->Input->setErrors(['errors' => [$result["error"]]]);
            }
        }

        return null;
    }

    /**
     * Updates the package for the service on the remote server. Sets Input
     * errors on failure, preventing the service's package from being changed.
     *
     * @param stdClass $package_from A stdClass object representing the current package
     * @param stdClass $package_to A stdClass object representing the new package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being changed (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function changeServicePackage(
        $package_from,
        $package_to,
        $service,
        $parent_package = null,
        $parent_service = null
    ) {
        return null; // Nothing to do
    }

    /**
     * Validates input data when attempting to add a package, returns the meta
     * data to save when adding a package. Performs any action required to add
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being added.
     *
     * @param array An array of key/value pairs used to add the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function addPackage(array $vars = null)
    {
        $meta = [];
        if (isset($vars['meta']) && is_array($vars['meta'])) {
            // Return all package meta fields
            foreach ($vars['meta'] as $key => $value) {
                $meta[] = [
                    'key' => $key,
                    'value' => $value,
                    'encrypted' => 0
                ];
            }
        }

        return $meta;
    }

    /**
     * Validates input data when attempting to edit a package, returns the meta
     * data to save when editing a package. Performs any action required to edit
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being edited.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array An array of key/value pairs used to edit the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editPackage($package, array $vars = null)
    {
        $meta = [];
        if (isset($vars['meta']) && is_array($vars['meta'])) {
            // Return all package meta fields
            foreach ($vars['meta'] as $key => $value) {
                $meta[] = [
                    'key' => $key,
                    'value' => $value,
                    'encrypted' => 0
                ];
            }
        }

        return $meta;
    }

    /**
     * Returns the rendered view of the manage module page
     *
     * @param mixed $module A stdClass object representing the module and its rows
     * @param array $vars An array of post data submitted to or on the manage module page
     *  (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the manager module page
     */
    public function manageModule($module, array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('manage', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'radwebhosting' . DS);

        #
        #
        # TODO: add tab to check status of all transfers:
        #
        #

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);

        $this->view->set('module', $module);

        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the add module row page
     *
     * @param array $vars An array of post data submitted to or on the add module
     *  row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the add module row page
     */
    public function manageAddRow(array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('add_row', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'radwebhosting' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);

        // Set unspecified checkboxes
        if (!empty($vars)) {
            if (empty($vars['sandbox'])) {
                $vars['sandbox'] = 'false';
            }
        }

        $vars['endpoint'] = 'https://radwebhosting.com/client_area/modules/addons/DomainsReseller/api/index.php';
        $this->view->set('vars', (object)$vars);
        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the edit module row page
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of post data submitted to or on the edit module
     *  row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the edit module row page
     */
    public function manageEditRow($module_row, array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('edit_row', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'radwebhosting' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);

        if (empty($vars)) {
            $vars = $module_row->meta;
        } else {
            // Set unspecified checkboxes
            if (empty($vars['sandbox'])) {
                $vars['sandbox'] = 'false';
            }
        }

        $this->view->set('vars', (object)$vars);
        return $this->view->fetch();
    }

    /**
     * Adds the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being added.
     *
     * @param array $vars An array of module info to add
     * @return array A numerically indexed array of meta fields for the module row containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function addModuleRow(array &$vars)
    {
        $fields = ['user', 'key', 'endpoint'];
        $this->Input->setRules($this->getRowRules($vars));

        // Validate module row
        if ($this->Input->validates($vars))
        {
            // Build the meta data for this row
            $meta = [];
            foreach ($vars as $key => $value)
            {
                if (in_array($key, $fields))
                {
                    $meta[] =
                    [
                        'key'       => $key,
                        'value'     => $value,
                        'encrypted' => 0
                    ];
                }
            }

            return $meta;
        }
    }

    /**
     * Edits the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being updated.
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of module info to update
     * @return array A numerically indexed array of meta fields for the module row containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function editModuleRow($module_row, array &$vars)
    {
        // Same as adding
        return $this->addModuleRow($vars);
    }

    /**
     * Deletes the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being deleted.
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     */
    public function deleteModuleRow($module_row)
    {
    }

    /**
     * Returns all fields used when adding/editing a package, including any
     * javascript to execute when the page is rendered with these fields.
     *
     * @param null $vars
     * @return ModuleFields
     * @throws Exception
     */
    public function getPackageFields($vars = null)
    {
        Loader::loadHelpers($this, ['Html']);
        $fields = new ModuleFields();

        //Add field to select type of the package
        $type = $fields->label(Language::_('radwebhosting.package_fields.type', true), 'radwebhosting_type');
        $type->attach(
            $fields->fieldSelect(
                'meta[type]',
                [
                    'domain'    => Language::_('radwebhosting.package_fields.type_domain.register', true),
                    //            'transfer'  => Language::_('radwebhosting.package_fields.type_domain.transfer', true),
                    //            'renew'     => Language::_('radwebhosting.package_fields.type_domain.renew', true),
                ],
                $this->Html->ifSet($vars->meta['type']),
                ['id' => 'radwebhosting_domaintype']
            )
        );
        $fields->setField($type);

        //Add field to select TLDs
        $tlds = $fields->label(Language::_('radwebhosting.package_fields.tld_options', true));

        $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
        $available = (new Calls\GetAvailableTlds($config))->process();
        foreach ($available as $tld)
        {
            $tld_label = $fields->label($tld, 'tld_' . $tld);
            $tlds->attach(
                $fields->fieldCheckbox(
                    'meta[tlds][]',
                    $tld,
                    (isset($vars->meta['tlds']) && in_array($tld, $vars->meta['tlds'])),
                    ['id' => 'tld_' . $tld],
                    $tld_label
                )
            );
        }
        $fields->setField($tlds);

        // Set nameservers
        for ($i=1; $i<=5; $i++) {
            $type = $fields->label(Language::_('radwebhosting.package_fields.ns' . $i, true), 'radwebhosting_ns' . $i);
            $type->attach(
                $fields->fieldText(
                    'meta[nameservers][ns][]',
                    $this->Html->ifSet($vars->meta['ns'][$i-1]),
                    ['id' => 'radwebhosting_ns' . $i]
                )
            );
            $fields->setField($type);
        }

        $fields->setHtml("
			<script type=\"text/javascript\">
				$(document).ready(function() {
					toggleTldOptions($('#radwebhosting_domaintype').val());

					// Re-fetch module options to pull cPanel packages and ACLs
					$('#radwebhosting_domaintype').change(function() {
						toggleTldOptions($(this).val());
					});

					function toggleTldOptions(type) {
						if (type == 'ssl')
							$('.radwebhosting_tlds').hide();
						else
							$('.radwebhosting_tlds').show();
					}
				});
			</script>
		");

        return $fields;
    }

    /**
     * Returns an array of key values for fields stored for a module, package,
     * and service under this module, used to substitute those keys with their
     * actual module, package, or service meta values in related emails.
     *
     * @return array A multi-dimensional array of key/value pairs where each key is one of 'module',
     *  'package', or 'service' and each value is a numerically indexed array of key values that match
     *  meta fields under that category.
     * @see Modules::addModuleRow()
     * @see Modules::editModuleRow()
     * @see Modules::addPackage()
     * @see Modules::editPackage()
     * @see Modules::addService()
     * @see Modules::editService()
     */
    public function getEmailTags()
    {
        return ['service' => ['domain']];
    }

    /**
     * Returns all fields to display to an admin attempting to add a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as
     *  any additional HTML markup to include
     */
    public function getAdminAddFields($package, $vars = null)
    {
        Loader::loadHelpers($this, ['Form', 'Html']);
        
        // Handle universal domain name
        if (isset($vars->domain))
            $vars->{'domain-name'} = $vars->domain;

        if ($package->meta->type == 'domain')
        {
            // Set default name servers
            if (!isset($vars->ns1) && isset($package->meta->ns))
            {
                $i=1;
                foreach ($package->meta->ns as $ns)
                {
                    $vars->{'ns' . $i++} = $ns;
                }
            }

            // Handle transfer request
            if (isset($vars->transfer) || isset($vars->EPPCode))
            {
                return $this->arrayToModuleFields(Configure::get('radwebhosting.transfer_fields'), null, $vars);
            }
            else
            {
                $module_fields = $this->arrayToModuleFields(
                        array_merge(Configure::get("radwebhosting.domain_fields"), Configure::get("radwebhosting.nameserver_fields")), 
                        null, 
                        $vars);	
                
		if (isset($vars->{'domain-name'}))
                {
                    $tld = $this->getTld($vars->{'domain-name'});
                    if ($tld) 
                    {
			$extension_fields = (array)Configure::get("radwebhosting.domain_fields" . $tld);
			if ($extension_fields)
                        {
                            $module_fields = $this->arrayToModuleFields($extension_fields, $module_fields, $vars);
                        }
                    }
		}
            }
        }

        return (isset($module_fields) ? $module_fields : new ModuleFields());
    }

    /**
     * Returns all fields to display to a client attempting to add a service with the module
     *
     * @param $package
     * @param null $vars
     * @return bool|ModuleFields
     * @throws Exception
     */
    public function getClientAddFields($package, $vars = null)
    {
        // Handle universal domain name
        // Handle universal domain name
        if (isset($vars->domain))
        {
            $vars->{'domain-name'} = $vars->domain;
        }
        
        if ($package->meta->type == 'domain')
        {
            // Set default name servers
            if (!isset($vars->ns) && isset($package->meta->ns))
            {
                $i=1;
                foreach ($package->meta->ns as $ns)
                {
                    $vars->{'ns' . $i++} = $ns;
                }
            }

            // Handle transfer request
            if (isset($vars->transfer) || isset($vars->EPPCode))
            {
                $fields = Configure::get('radwebhosting.transfer_fields');

                // We should already have the domain name don't make editable
                $fields['domain']['type'] = 'hidden';
                $fields['domain']['label'] = null;

                return $this->arrayToModuleFields($fields, null, $vars);
            }
            else
            {
                // Handle domain registration
                $fields = array_merge(
                    Configure::get('radwebhosting.nameserver_fields'),
                    Configure::get('radwebhosting.domain_fields')
                );

                // We should already have the domain name don't make editable
                $fields['domain']['type'] = 'hidden';
                $fields['domain']['label'] = null;

                $module_fields = $this->arrayToModuleFields($fields, null, $vars);

                if (isset($vars->{'domain-name'}))
                {
                    $tld = $this->getTld($vars->{'domain-name'});
                    $extension_fields = array_merge((array)Configure::get("radwebhosting.domain_fields" . $tld), (array)Configure::get("radwebhosting.contact_fields" . $tld));
                    if ($extension_fields)
                    {
			$module_fields = $this->arrayToModuleFields($extension_fields, $module_fields, $vars);
                    }
		}
            }
        }

        // Determine whether this is an AJAX request
        return (isset($module_fields) ? $module_fields : new ModuleFields());
    }

    /**
     * Builds and returns the module fields for domain registration
     *
     * @param $vars
     * @param bool $client
     * @return bool|ModuleFields
     * @throws Exception
     */
    private function buildDomainModuleFields($vars, $client = false)
    {
        if (isset($vars->domain))
        {
            $tld = $this->getTld($vars->domain);

            $extension_fields = Configure::get('radwebhosting.domain_fields' . $tld);
            if ($extension_fields)
            {
                // Set the fields
                if ($client)
                {
                    $fields = array_merge(
                        Configure::get('radwebhosting.nameserver_fields'),
                        Configure::get('radwebhosting.domain_fields'),
                        $extension_fields
                    );
                }
                else
                {
                    $fields = array_merge(
                        Configure::get('radwebhosting.domain_fields'),
                        Configure::get('radwebhosting.nameserver_fields'),
                        $extension_fields
                    );
                }

                if ($client)
                {
                    // We should already have the domain name don't make editable
                    $fields['domain']['type']  = 'hidden';
                    $fields['domain']['label'] = null;
                }

                // Build the module fields
                $module_fields = new ModuleFields();

                // Allow AJAX requests
                $ajax = $module_fields->fieldHidden('allow_ajax', 'true', ['id'=>'radwebhosting_allow_ajax']);
                $module_fields->setField($ajax);

                foreach ($fields as $key => $field)
                {
                    // Build the field
                    $label = $module_fields->label((isset($field['label']) ? $field['label'] : ''), $key);

                    switch($field['type'])
                    {
                        case "text":
                            $type = $module_fields->fieldText($key, (isset($vars->{$key}) ? $vars->{$key} : ''),['id' => $key]);
                            break;
                        case "select":
                            $please_select = ['' => Language::_('AppController.select.please', true)];
                            $type = $module_fields->fieldSelect($key, (isset($field['options']) ? $please_select + $field['options'] : $please_select), (isset($vars->{$key}) ? $vars->{$key} : ''), ['id' => $key]);
                            break;
                        case "hidden":
                            $type = $module_fields->fieldHidden($key, (isset($vars->{$key}) ? $vars->{$key} : ''), ['id' => $key]);
                            break;
                    }

                    // Include a tooltip if set
                    if (!empty($field['tooltip']))
                    {
                        $label->attach($module_fields->tooltip($field['tooltip']));
                    }

                    if ($type)
                    {
                        $label->attach($type);
                        $module_fields->setField($label);
                    }
                }
            }
        }

        return (isset($module_fields) ? $module_fields : false);
    }

    /**
     * Returns all fields to display to an admin attempting to edit a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well
     *  as any additional HTML markup to include
     */
    public function getAdminEditFields($package, $vars = null)
    {
        if ($package->meta->type == 'domain') {
            return new ModuleFields();
        } else {
            return new ModuleFields();
        }
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * admin interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getAdminServiceInfo($service, $package)
    {
        return '';
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * client interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getClientServiceInfo($service, $package)
    {
        return '';
    }

    /**
     * Returns all tabs to display to an admin when managing a service whose
     * package uses this module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title.
     *  Example: array('methodName' => "Title", 'methodName2' => "Title2")
     */
    public function getAdminTabs($package)
    {
        if ($package->meta->type == 'domain')
        {
            return
            [
                'tabWhois' => Language::_('radwebhosting.tab_whois.title', true),
                'tabNameservers' => Language::_('radwebhosting.tab_nameservers.title', true),
                'tabSettings' => Language::_('radwebhosting.tab_settings.title', true)
            ];
        }
    }

    /**
     * Returns all tabs to display to a client when managing a service whose
     * package uses this module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title.
     *  Example: array('methodName' => "Title", 'methodName2' => "Title2")
     */
    public function getClientTabs($package)
    {
        if ($package->meta->type == 'domain')
        {
            return
            [
                'tabClientWhois' => Language::_('radwebhosting.tab_whois.title', true),
                'tabClientNameservers' => Language::_('radwebhosting.tab_nameservers.title', true),
                'tabClientSettings' => Language::_('radwebhosting.tab_settings.title', true)
            ];
        }
    }

    /**
     * Admin Whois tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabWhois($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->manageWhois('tab_whois', $package, $service, $get, $post, $files);
    }

    /**
     * Client Whois tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabClientWhois($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->manageWhois('tab_client_whois', $package, $service, $get, $post, $files);
    }

    /**
     * Admin Nameservers tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabNameservers($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->manageNameservers('tab_nameservers', $package, $service, $get, $post, $files);
    }

    /**
     * Admin Nameservers tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabClientNameservers($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->manageNameservers('tab_client_nameservers', $package, $service, $get, $post, $files);
    }

    /**
     * Admin Settings tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabSettings($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->manageSettings('tab_settings', $package, $service, $get, $post, $files);
    }

    /**
     * Client Settings tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabClientSettings($package, $service, array $get = null, array $post = null, array $files = null)
    {
        return $this->manageSettings('tab_client_settings', $package, $service, $get, $post, $files);
    }

    /**
     * Handle updating whois information
     *
     * @param string $view The view to use
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    private function manageWhois($view, $package, $service, array $get = null, array $post = null, array $files = null)
    {
        $this->view = new View($view, 'default');
        Loader::loadHelpers($this, ['Form', 'Html']);
        $fields = $this->serviceFieldsToObject($service->fields);

        $vars = new \stdClass();
        if (!empty($post))
        {
            $types          = ["Registrant", "Technical", "Billng", "Admin"];
            $contactdetails = [];
            foreach($types as $type)
            {
                foreach($post as $key => $value)
                {
                    if(strpos($key, $type) !== false)
                    {
                        $key = str_replace($type, "", $key);
                        $contactdetails[$type][$key] = $value;
                    }
                }
            }

            $postfields =
            [
                "domain"            => $fields->domain,
                "contactdetails"    => $contactdetails
            ];

            $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
            (new Calls\SaveContactDetails($config, $postfields))->process();
        }

        //Get Domain Whois fields
        $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
        $data = (new Calls\GetContactDetails($config, ['domain' => $fields->domain]))->process();

        // Format fields
        $sections = [];
        foreach ($data as $section => $element)
        {
            $sections[] = $section;
            foreach ($element as $name => $value)
            {
                // Value must be a string
                if (!is_scalar($value))
                {
                    $value = '';
                }

                $vars->{$section . $name} = $value;
                $whoisFields[$section . $name] =
                [
                    'label' => $name,
                    'type' => 'text'
                ];
            }
        }

        $this->view->set('vars', $vars);
        $this->view->set('fields', $this->arrayToModuleFields($whoisFields, null, $vars)->getFields());
        $this->view->set('sections', $sections);
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'radwebhosting' . DS);
        return $this->view->fetch();
    }

    /**
     * Handle updating nameserver information
     *
     * @param string $view The view to use
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    private function manageNameservers($view, $package, $service, array $get = null, array $post = null, array $files = null)
    {
        $this->view = new View($view, 'default');
        Loader::loadHelpers($this, ['Form', 'Html']);
        $fields = $this->serviceFieldsToObject($service->fields);

        $vars = new stdClass();
        if (!empty($post))
        {
            $postfields = ['domain' => $fields->domain];
            foreach($post["ns"] as $key => $ns)
            {
                $i = $key+1;
                $postfields["ns{$i}"] = $ns;
            }

            $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
            (new Calls\SaveNameServers($config, $postfields))->process();
        }

        $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
        $data = (new Calls\GetNameServers($config, ['domain' => $fields->domain]))->process();

        foreach($data as $nameserver)
        {
            $vars->ns[] = $nameserver;
        }

        $this->view->set('vars', $vars);
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'radwebhosting' . DS);
        return $this->view->fetch();
    }

    /**
     * Handle updating settings
     *
     * @param $view
     * @param $package
     * @param $service
     * @param array|null $get
     * @param array|null $post
     * @param array|null $files
     * @return mixed
     * @throws Exception
     */
    private function manageSettings($view, $package, $service, array $get = null, array $post = null, array $files = null)
    {
        $this->view = new View($view, 'default');
        Loader::loadHelpers($this, ['Form', 'Html']);
        $fields = $this->serviceFieldsToObject($service->fields);

        $vars = new stdClass();
        if (!empty($post))
        {
            if (isset($post['registrar_lock']))
            {
                $postfields =
                [
                    "domain"        => $fields->domain,
                    "lockstatus"    => $post['registrar_lock'] == 'true' ? "1" : "0"
                ];

                $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
                (new Calls\SaveRegistrarLock($config, $postfields))->process();
            }

            if (isset($post['request_epp']))
            {
                $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
                (new Calls\GetEppCode($config, ['domain' => $fields->domain]))->process();
            }
        }

        $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
        $result = (new Calls\GetRegistrarLock($config, ['domain' => $fields->domain]))->process();
        $vars->registrar_lock = $result == "locked" ? "true" : "false";

        $this->view->set('vars', $vars);
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'radwebhosting' . DS);
        return $this->view->fetch();
    }

    /**
     * @param $domain
     * @return mixed
     * @throws Exception
     */
    public function checkAvailability($domain)
    {
        $postfields =
        [
            "searchTerm"            => $domain,
            "punyCodeSearchTerm"    => "",
            "tldsToInclude"         => [$this->getTld($domain)],
            "isIdnDomain"           => "",
            "premiumEnabled"        => ""
        ];

        $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
        $result = (new Calls\CheckAvailability($config, $postfields))->process();

        return $result[0]["isAvailable"];
    }

    /**
     * Builds and returns the rules required to add/edit a module row
     *
     * @param array $vars An array of key/value data pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    private function getRowRules(&$vars)
    {
        return
        [
            'user' =>
            [
                'valid' =>
                [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('radwebhosting.!error.user.valid', true)
                ]
            ],
            'key' =>
            [
                'valid' =>
                [
                    'last' => true,
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('radwebhosting.!error.key.valid', true)
                ],
            ],
            'endpoint' =>
            [
                'valid' =>
                [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('radwebhosting.!error.endpoint.valid', true)
                ],
            ]
        ];
    }

    /**
     * Returns the TLD of the given domain
     *
     * @param $domain
     * @return string
     * @throws Exception
     */
    private function getTld($domain)
    {
        $config = Core\Configuration::create($this->getModuleRows()[0]->meta);
        $tlds = (new Calls\GetAvailableTlds($config))->process();

        $domain = strtolower($domain);
        foreach ($tlds as $tld)
        {
            if (substr($domain, -strlen($tld)) == $tld)
            {
                return $tld;
            }
        }

        return strstr($domain, '.');
    }

    /**
     * Formats a phone number into +NNN.NNNNNNNNNN
     *
     * @param string $number The phone number
     * @param string $country The ISO 3166-1 alpha2 country code
     * @return string The number in +NNN.NNNNNNNNNN
     */
    private function formatPhone($number, $country)
    {
        if (!isset($this->Contacts)) {
            Loader::loadModels($this, ['Contacts']);
        }

        return $this->Contacts->intlNumber($number, $country, '.');
    }
}
