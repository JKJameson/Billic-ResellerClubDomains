<?php
class ResellerClubDomains {
	public $settings = array(
		'orderform_vars' => array(
			'domain',
			'extension',
			'action',
			'transfer_code'
		) ,
		'description' => 'Automate the registration of domain names via ResellerClub.',
	);
	function user_cp($array) {
		global $billic, $db;
		$service = $array['service'];
		if (!empty($_GET['Action'])) {
			switch ($_GET['Action']) {
				case 'NameServers':
					$dom = $this->curl('api/domains/details-by-name.json?domain-name=' . urlencode($service['domain']) . '&options=All');
					if (isset($_POST['update'])) {
						$url = 'api/domains/modify-ns.json?order-id=' . $dom['orderid'];
						$post = '';
						for ($i = 1;$i <= 8;$i++) {
							if (empty($_POST['ns' . $i])) {
								break;
							}
							$post.= 'ns=' . urlencode($_POST['ns' . $i]) . '&';
						}
						$post = substr($post, 0, -1);
						$dom = $this->curl($url, $post);
						if ($dom['status'] == 'Success') {
							echo '<b><font color="green">Successfully Updated!</font></b>';
						} else {
							err('Error setting name servers: ' . $json);
						}
						exit;
					}
					$billic->show_errors();
					echo '<form method="POST"><table class="table table-striped"><tr><th>#</th><th>Nameserver</th></tr>';
					$total = $dom['noOfNameServers'];
					if ($total < 2) {
						$total = 2;
					}
					for ($i = 1;$i <= $total;$i++) {
						echo '<tr><td>' . $i . '</td><td><input type="text" class="form-control" name="ns' . $i . '" value="' . safe($dom['ns' . $i]) . '" style="width: 250px"></td></tr>';
					}
					echo '<tr><td colspan="2" align="center"><input type="submit" class="btn btn-success" name="update" value="Update &raquo;" onClick="javascript:this.value=\'Updating. Please wait...\';this.readOnly=true"></table></form>';
					exit;
				break;
				case 'ChangeContactDetails':
					$dom_url = 'api/domains/details-by-name.json?domain-name=' . urlencode($service['domain']) . '&options=All';
					$dom = $this->curl($dom_url);
					$contactid = $dom['billingcontact']['contactid'];
					if (isset($_POST['update'])) {
						$post = 'contact-id=' . urlencode($contactid) . '&name=' . urlencode($_POST['name']) . '&' . '&company=' . urlencode($_POST['company']) . '&' . '&email=' . urlencode($_POST['email']) . '&' . '&address-line-1=' . urlencode($_POST['address-line-1']) . '&' . '&address-line-2=' . urlencode($_POST['address-line-2']) . '&' . '&address-line-3=' . urlencode($_POST['address-line-3']) . '&' . '&city=' . urlencode($_POST['city']) . '&' . '&state=' . urlencode($_POST['state']) . '&' . '&zipcode=' . urlencode($_POST['zipcode']) . '&' . '&country=' . urlencode($_POST['country']) . '&' . '&phone-cc=' . urlencode($_POST['phone-cc']) . '&' . '&name=' . urlencode($_POST['phone']);
						$add = $this->curl('api/contacts/modify.json', $post);
						$dom = $this->curl($dom_url);
					}
					$billic->show_errors();
					echo '<form method="POST"><table class="table table-striped">';
					echo '<tr><td>Name:</td><td><input type="text" class="form-control" name="name" value="' . safe($dom['billingcontact']['name']) . '"></td></tr>';
					echo '<tr><td>Company:</td><td><input type="text" class="form-control" name="company" value="' . safe($dom['billingcontact']['company']) . '"></td></tr>';
					echo '<tr><td>Email:</td><td><input type="text" class="form-control" name="email" value="' . safe($dom['billingcontact']['emailaddr']) . '"></td></tr>';
					echo '<tr><td>Address Line 1:</td><td><input type="text" class="form-control" name="address-line-1" value="' . safe($dom['billingcontact']['address1']) . '"></td></tr>';
					echo '<tr><td>Address Line 2:</td><td><input type="text" class="form-control" name="address-line-2" value="' . safe($dom['billingcontact']['address2']) . '"></td></tr>';
					echo '<tr><td>Address Line 3:</td><td><input type="text" class="form-control" name="address-line-3" value="' . safe($dom['billingcontact']['address3']) . '"></td></tr>';
					echo '<tr><td>City:</td><td><input type="text" class="form-control" name="city" value="' . safe($dom['billingcontact']['city']) . '"></td></tr>';
					echo '<tr><td>State:</td><td><input type="text" class="form-control" name="state" value="' . safe($dom['billingcontact']['state']) . '"></td></tr>';
					echo '<tr><td>Zip Code:</td><td><input type="text" class="form-control" name="zipcode" value="' . safe($dom['billingcontact']['zip']) . '"></td></tr>';
					echo '<tr><td>Country:</td><td><input type="text" class="form-control" name="country" value="' . safe($dom['billingcontact']['country']) . '"></td></tr>';
					echo '<tr><td>Phone:</td><td><div class="form-group"><div class="col-sm-1"><input type="text" class="form-control" value="+" readonly></div><div class="col-sm-1"><input type="text" class="form-control" name="phone-cc" value="' . safe($dom['billingcontact']['telnocc']) . '" maxlength="3"></div><div class="col-sm-9"><input type="text" class="form-control" name="phone" value="' . safe($dom['billingcontact']['telno']) . '"></div></div></td></tr>';
					echo '<tr><td colspan="2" align="center"><input type="submit" class="btn btn-success" name="update" value="Update &raquo;" onClick="javascript:this.value=\'Updating. Please wait...\';this.readOnly=true"></table></form>';
					exit;
				break;
				case 'PrivacyProtection':
					$dom = $this->curl('api/domains/details-by-name.json?domain-name=' . urlencode($service['domain']) . '&options=All');
					if (isset($_POST['action'])) {
						if ($_POST['action'] == 'Disable') {
							$action = 'false';
						} else if ($_POST['action'] == 'Enable') {
							$action = 'true';
						} else {
							err('Invalid Action');
						}
						$url = 'api/domains/modify-privacy-protection.json?order-id=' . $dom['orderid'] . '&protect-privacy=' . $action . '&reason=User_Request';
						$dom = $this->curl($url, '');
						if ($dom['status'] == 'Success') {
							echo '<b><font color="green">Successfully Updated!</font></b>';
						} else {
							err('Error setting privacy protection: ' . $json);
						}
						exit;
					}
					$billic->show_errors();
					echo '<form method="POST">';
					if ($dom['isprivacyprotected'] == 'true') {
						echo 'Privacy Protection is Enabled. <input type="submit" class="btn btn-danger" name="action" value="Disable">';
					} else if ($dom['isprivacyprotected'] == 'false') {
						echo 'Privacy Protection is Disabled. <input type="submit" class="btn btn-success" name="action" value="Enable">';
					} else {
						echo 'Unable to get Privacy Protection status at this time.';
					}
					echo '</form>';
					exit;
					break;
				case 'TheftProtection':
					$dom = $this->curl('api/domains/details-by-name.json?domain-name=' . urlencode($service['domain']) . '&options=All');
					$locks = $this->curl('api/domains/locks.json?order-id=' . $dom['orderid']);
					if (isset($_POST['action'])) {
						if ($_POST['action'] == 'Disable') {
							$url = 'api/domains/disable-theft-protection.json?order-id=' . $dom['orderid'];
							$dom = $this->curl($url, '');
							if ($dom['status'] == 'Success') {
								echo '<b><font color="green">Successfully Updated!</font></b>';
							} else {
								err('Error setting transfer lock: ' . $json);
							}
						} else if ($_POST['action'] == 'Enable') {
							$url = 'api/domains/enable-theft-protection.json?order-id=' . $dom['orderid'];
							$dom = $this->curl($url, '');
							if ($dom['status'] == 'Success') {
								echo '<b><font color="green">Successfully Updated!</font></b>';
							} else {
								err('Error setting transfer lock: ' . $json);
							}
						} else {
							err('Invalid Action');
						}
						exit;
					}
					$billic->show_errors();
					echo '<form method="POST">';
					if ($locks['transferlock'] == 'true') {
						echo 'Transfer Lock is Enabled. <input type="submit" class="btn btn-danger" name="action" value="Disable">';
					} else {
						echo 'Transfer Lock is Disabled. <input type="submit" class="btn btn-success" name="action" value="Enable">';
					}
					echo '</form>';
					exit;
					break;
				case 'TransferAuthCode':
					$dom = $this->curl('api/domains/details-by-name.json?domain-name=' . urlencode($service['domain']) . '&options=All');
					$locks = $this->curl('api/domains/locks.json?order-id=' . $dom['orderid']);
					if (isset($_POST['generate'])) {
						$auth_code = $billic->rand_str(4) . '-' . $billic->rand_str(4) . '-' . $billic->rand_str(4);
						$url = 'api/domains/modify-auth-code.json?order-id=' . $dom['orderid'] . '&auth-code=' . $auth_code;
						$dom = $this->curl($url, '');
						if ($dom['status'] == 'Success') {
							echo '<b><font color="green">Your Transfer Auth Code has been set to: ' . $auth_code . '</font></b>';
						} else {
							err('Error setting transfer lock: ' . $json);
						}
						exit;
					}
					$billic->show_errors();
					echo '<form method="POST">';
					if ($locks['transferlock'] == 'true') {
						echo 'Transfer Lock is Enabled. Please disable it first.';
					} else {
						echo '<input type="submit" class="btn btn-success" name="generate" value="Generate a new Transfer Auth Code for this domain">';
					}
					echo '</form>';
					exit;
					break;
				case 'ChildNameServers':
					$dom_url = 'api/domains/details-by-name.json?domain-name=' . urlencode($service['domain']) . '&options=All';
					$dom = $this->curl($dom_url);
					if (isset($_POST['update'])) {
						if (!empty($_POST['new_cns']) && !empty($_POST['new_ip'])) {
							$post = 'order-id=' . $dom['orderid'] . '&cns=' . urlencode($_POST['new_cns'] . '.' . $service['domain']);
							foreach ($dom['cns'] as $cns => $ips) {
								if ($cns == $_POST['new_cns'] . '.' . $service['domain']) {
									foreach ($ips as $ip) {
										if (empty($ip)) {
											continue;
										}
										$post.= '&ip=' . urlencode($ip);
									}
								}
							}
							$post.= '&ip=' . urlencode($_POST['new_ip']);
							$add = $this->curl('api/domains/add-cns.json', $post);
							if (!array_key_exists('status', $add)) {
								echo '<b><font color="green">Successfully Updated!</font></b>';
							} else {
								err('API Error: ' . $add['status']);
							}
						}
						foreach ($_POST['cns'] as $cns => $ips) {
							foreach ($ips as $ip) {
								//if (!in_array($ip, $dom['cns'][$cns]
								//https://httpapi.com/api/domains/modify-cns-ip.json?auth-userid=0&api-key=key&order-id=0&cns=ns1.domain.com&old-ip=0.0.0.0&new-ip=1.1.1.1
								if (empty($ip)) {
									continue;
								}
								$post.= '&ip=' . urlencode($ip);
							}
						}
						$dom = $this->curl($dom_url);
					}
					$billic->show_errors();
					echo '<form method="POST"><table class="table table-striped"><tr><tr><th>Name Servers</th><th>IP Address</th></tr>';
					foreach ($dom['cns'] as $cns => $ips) {
						foreach ($ips as $ip) {
							echo '<tr><td>' . safe($cns) . '</td><td><input type="text" class="form-control" name="cns[' . $cns . '][]" value="' . safe($ip) . '" style="width: 250px"></td></tr>';
						}
					}
					echo '<tr><td><input type="text" class="form-control" name="new_cns" value="" style="width: 50px">.' . $service['domain'] . '</td><td><input type="text" class="form-control" name="new_ip" value="" style="width: 250px"></td></tr>';
					echo '<tr><td colspan="2" align="center"><input type="submit" class="btn btn-default" name="update" value="Update &raquo;" onClick="javascript:this.value=\'Updating. Please wait...\';this.readOnly=true"></table></form>';
					exit;
					break;
				}
			}
			echo '<ul class="nav nav-pills">';
			echo '<li role="presentation" class="active"><a href="' . $billic->uri() . 'Action/NameServers/"><i class="icon-globe-world"></i> Name Servers</a></li>';
			echo '<li role="presentation" class="active"><a href="' . $billic->uri() . 'Action/ChangeContactDetails/"><i class="icon-user"></i>  Contact Details</a></li>';
			echo '<li role="presentation" class="active"><a href="' . $billic->uri() . 'Action/PrivacyProtection/"><i class="icon-clipboard"></i> Privacy Protection</a></li>';
			echo '<li role="presentation" class="active"><a href="' . $billic->uri() . 'Action/TheftProtection/"><i class="icon-shield"></i> Theft Protection</a></li>';
			echo '<li role="presentation" class="active"><a href="' . $billic->uri() . 'Action/TransferAuthCode/"><i class="icon-refresh"></i> Get Transfer Auth Code</a></li>';
			// <a href="'.$billic->uri().'Action/ChildNameServers/"><img src="/i/icons/world_edit.png" class="inline16">Register a Child Name Server</a><br><br>
			echo '</ul>';
		}
		function suspend($array) {
			global $billic, $db;
			$service = $array['service'];
			return true;
		}
		function unsuspend($array) {
			global $billic, $db;
			$service = $array['service'];
			return true;
		}
		function terminate($array) {
			global $billic, $db;
			$service = $array['service'];
			return true;
		}
		function create($array) {
			global $billic, $db;
			$vars = $array['vars'];
			$service = $array['service'];
			$plan = $array['plan'];
			$user_row = $array['user'];
			$password = $billic->rand_str(15);
			if (empty($user_row['postcode'])) {
				$user_row['postcode'] = '0000';
			}
			$rc_user_url = 'https://httpapi.com/api/customers/details.json?auth-userid=' . get_config('resellerclub_resellerid') . '&api-key=' . get_config('resellerclub_apikey') . '&username=' . urlencode($user_row['email']);
			$rc_user = @file_get_contents($rc_user_url);
			if (!$rc_user) {
				return 'Failed to communicate with API.';
			}
			$rc_user = json_decode($rc_user, true);
			if (!$rc_user) {
				return 'Invalid data returned from API.';
				return;
			}
			if ($rc_user['status'] == 'ERROR') {
				if (strpos($rc_user['message'], 'Customer') !== false && strpos($rc_user['message'], 'not found') !== false) { // user not found
					if (empty($user_row['companyname'])) {
						$user_row['companyname'] = 'N/A';
					}
					$register = @file_get_contents('https://httpapi.com/api/customers/signup.xml?auth-userid=' . get_config('resellerclub_resellerid') . '&api-key=' . get_config('resellerclub_apikey') . '&username=' . urlencode($user_row['email']) . '&passwd=' . urlencode($password) . '&name=' . urlencode($user_row['firstname'] . ' ' . $user_row['lastname']) . '&company=' . urlencode($user_row['companyname']) . '&address-line-1=' . urlencode($user_row['address1']) . '&address-line-2=' . urlencode($user_row['address2']) . '&city=' . urlencode($user_row['city']) . '&state=' . urlencode($user_row['state']) . '&country=' . urlencode($user_row['country']) . '&zipcode=' . urlencode($user_row['postcode']) . '&phone-cc=0&phone=' . urlencode($user_row['phonenumber']) . '&lang-pref=en');
					if (!$register) {
						return 'API Returned error while trying to register customer: ' . $register;
					}
					$register = json_decode(json_encode((array)simplexml_load_string($register)) , 1);
					if (!empty($register['message'])) {
						return 'API Returned error while trying to register customer: ' . $register['message'];
					}
					$rc_user = @file_get_contents($rc_user_url);
					if (!$rc_user) {
						return 'Failed to communicate with API.';
					}
					$rc_user = json_decode($rc_user, true);
					if (!$rc_user) {
						return 'Invalid data returned from API.';
						return;
					}
					if ($rc_user['status'] == 'ERROR') {
						return 'API Error: ' . $rc_user['message'];
						return;
					}
				} else {
					return 'API Error: ' . $rc_user['message'];
					return;
				}
			}
			$contacts = @file_get_contents('https://httpapi.com/api/contacts/default.json?auth-userid=' . get_config('resellerclub_resellerid') . '&api-key=' . get_config('resellerclub_apikey') . '&customer-id=' . urlencode($rc_user['customerid']) . '&type=Contact');
			if (!$contacts) {
				return 'Failed to communicate with API.';
			}
			$contacts = json_decode($contacts, true);
			if (!$contacts) {
				return 'Invalid data returned from API.';
				return;
			}
			if ($contacts['status'] == 'ERROR') {
				return 'API Error: ' . $contacts['message'];
				return;
			}
			$ns = @file_get_contents('https://httpapi.com/api/domains/customer-default-ns.json?auth-userid=' . get_config('resellerclub_resellerid') . '&api-key=' . get_config('resellerclub_apikey') . '&customer-id=' . urlencode($rc_user['customerid']));
			if (!$ns) {
				return 'Failed to communicate with API.';
			}
			$ns = json_decode($ns, true);
			if (!$ns) {
				return 'Invalid data returned from API.';
				return;
			}
			if ($ns['status'] == 'ERROR') {
				return 'API Error: ' . $ns['message'];
				return;
			}
			$url = 'https://httpapi.com/api/domains/register.xml?auth-userid=' . get_config('resellerclub_resellerid') . '&api-key=' . get_config('resellerclub_apikey') . '&domain-name=' . urlencode($service['domain']) . '&years=1';
			foreach ($ns as $n) {
				$url.= '&ns=' . urlencode($n);
			}
			$url.= '&customer-id=' . urlencode($rc_user['customerid']) . '&reg-contact-id=' . $contacts['Contact']['registrant'] . '&admin-contact-id=' . $contacts['Contact']['admin'] . '&tech-contact-id=' . $contacts['Contact']['tech'] . '&billing-contact-id=' . $contacts['Contact']['billing'] . '&invoice-option=NoInvoice&protect-privacy=true&attr-name1=idnLanguageCode&attr-value1=aze';
			$ctx = stream_context_create(array(
				'http' => array(
					'timeout' => 60
				)
			));
			$register = @file_get_contents($url, false, $ctx);
			var_dump($register, $rc_user);
			if (!$register) {
				return 'Failed to communicate with API.';
			}
			$register = json_decode($register, true);
			if (!$register) {
				return 'Invalid data returned from API.';
				return;
			}
			if ($register['status'] == 'ERROR') {
				return 'API Error: ' . $register['message'];
				return;
			}
			if (is_numeric($register['entityid'])) {
				return true;
			} else {
				return 'An unknown error occurred';
			}
		}
		function ordercheck($array) {
			global $billic, $db;
			$vars = $array['vars'];
			if (!ctype_alnum(str_replace('-', '', $vars['domain']))) {
				$billic->error('Invalid Domain. Please enter the domain without any dots or extension.', 'domain');
				return;
			}
			if ($vars['action'] == 'Register') {
				$available = @file_get_contents('https://httpapi.com/api/domains/available.json?auth-userid=' . get_config('resellerclub_resellerid') . '&api-key=' . get_config('resellerclub_apikey') . '&domain-name=' . urlencode($vars['domain']) . '&tlds=' . urlencode($vars['extension']));
				if (!$available) {
					$billic->error('The domain checker is currently unavailable. Please try again later. If the problem persists, please contact support.', 'domain');
					return;
				}
				$available = json_decode($available, true);
				if (!$available) {
					$billic->error('Invalid data returned from API. Please try again later. If the problem persists, please contact support.', 'domain');
					return;
				}
				if ($available['status'] == 'ERROR') {
					$billic->error('API Error: ' . $available['message'], 'domain');
					return;
				}
				foreach ($available as $domain => $array) {
					if ($array['status'] == 'regthroughothers' || $array['status'] == 'regthroughus') {
						$billic->error($domain . ' is already registered', 'domain');
						return;
					}
				}
			}
			if ($vars['action'] == 'Transfer' && empty($vars['transfer_code'])) {
				$billic->error('Transfer code is required if transferring a domain.', 'transfer_code');
				return;
			}
			return $vars['domain'] . '.' . $vars['extension']; // return the domain for the service to be called
			
		}
		function curl($url, $post = false) {
			$options = array(
				CURLOPT_URL => 'https://httpapi.com/' . $url . '&auth-userid=' . urlencode(get_config('resellerclub_resellerid')) . '&api-key=' . urlencode(get_config('resellerclub_apikey')) ,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_USERAGENT => "Curl",
				CURLOPT_AUTOREFERER => true,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 300,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => false,
				//CURLOPT_VERBOSE			=> 1,
				
			);
			if ($post !== false) {
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = $post;
			}
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			$data = curl_exec($ch);
			if ($data === false) {
				err('Curl error: ' . curl_error($ch));
			}
			$data = trim($data);
			$json = json_decode($data, true);
			if ($json['status'] == 'ERROR') {
				err('Domain API error: ' . $json['message']);
			}
			return $json;
		}
		function settings($array) {
			global $billic, $db;
			if (empty($_POST['update'])) {
				echo '<form method="POST"><input type="hidden" name="billic_ajax_module" value="ResellerClubDomains"><table class="table table-striped">';
				echo '<tr><th>Setting</th><th>Value</th></tr>';
				echo '<tr><td>ResellerClub Reseller ID</td><td><input type="text" class="form-control" name="resellerclub_resellerid" value="' . safe(get_config('resellerclub_resellerid')) . '" style="width: 100%"></td></tr>';
				echo '<tr><td>ResellerClub API Key</td><td><input type="text" class="form-control" name="resellerclub_apikey" value="' . safe(get_config('resellerclub_apikey')) . '" style="width: 100%"></td></tr>';
				echo '<tr><td colspan="2" align="center"><input type="submit" class="btn btn-default" name="update" value="Update &raquo;"></td></tr>';
				echo '</table></form>';
			} else {
				if (empty($billic->errors)) {
					set_config('resellerclub_resellerid', $_POST['resellerclub_resellerid']);
					set_config('resellerclub_apikey', $_POST['resellerclub_apikey']);
					$billic->status = 'updated';
				}
			}
		}
}
