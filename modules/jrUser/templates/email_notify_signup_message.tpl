A new user has just signed up on {$system_name}:

user name: {$user_name}
email address: {$user_email}
ip address: {$ip_address}

You can view the new User Profile here:

{$new_profile_url}

{if isset($signup_method) && $signup_method == 'admin'}
Pending User Dashboard:

{$_conf.jrCore_base_url}/{jrCore_module_url module="jrCore"}/dashboard/pending
{/if}



