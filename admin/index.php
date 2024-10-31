<?php
// to get whmcs Saved Url from database
$db_whmcs_url = get_option('whmcs_url', '');
// Save Whmcs URL in DB
function pf_whmcs_saveSetting($whmcs_url)
{
    if (is_null($whmcs_url)) {
        return 'error';
    } else {
        $whmcs_url_validity = pf_whmcs_testUrl($whmcs_url);
        if ($whmcs_url_validity) {
            // if url is valid then we have to save it in DB
            // add_option routine to save a value into the options database table
            add_option('whmcs_url', $whmcs_url, '', 'no');
            return 'success';
        } else {
            return 'error';
        }
    }
}
// Update Whmcs_url in database
function pf_whmcs_updateSetting($whmcs_url)
{
    if (is_null($whmcs_url)) {
        return 'error';
    } else {
        $whmcs_url_validity = pf_whmcs_testUrl($whmcs_url);
        if ($whmcs_url_validity) {
            // if url is valid then we have to update it in DB
            // Update a saved database option using the update_option routine
            update_option('whmcs_url', $whmcs_url, 'no');
            return 'success';
        } else {
            return 'error';
        }
    }
}
// this function return 1 if provide url is invalid and 0 if valid
function pf_whmcs_testUrl($base_url)
{
    $final_url = $base_url . '/feeds/domainchecker.php';
    $whmcs_url_validity = 0;
    // first of all check if url is valid or not
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        $valid = 0;
    } else {
        $valid = 1;
    }
    if ($valid) {
        // if URL is valid now its time to check if its WHMCS's url or not
        $result = wp_remote_post($final_url);
        $httpCode = isset($result['response']['code']) ? $result['response']['code'] : 404;
        if ($httpCode != 404) {
            $whmcs_url_validity = 1;
        }
    }
    return $whmcs_url_validity;
}
?>
<div class="row mt-2">
    <h1>Products Fetcher for WHMCS</h1>
    <label>Dynamic way for extracting price from WHMCS for the use on the page of your Website!</label>
    <br><hr><br>
    <div class="col-md-12">
        <form method="POST" class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <label>WHMCS URL</label>
                    <input type="text" name="whmcs_url" id="whmcs_url" class="whmcs_url" value="<?php echo esc_url($db_whmcs_url); ?>" placeholder="https://www.example.com/whmcs-template"/>
                </div>
                <?php
                if (array_key_exists('save_settings', $_POST)) {
                    $save_response = pf_whmcs_saveSetting(esc_url_raw($_POST['whmcs_url']));
                } elseif (array_key_exists('update_settings', $_POST)) {
                    $save_response = pf_whmcs_updateSetting(esc_url_raw($_POST['whmcs_url']));
                } elseif (array_key_exists('test_url', $_POST)) {
                    $whmcs_url_validity = pf_whmcs_testUrl(esc_url_raw($_POST['whmcs_url']));
                }
                if (isset($whmcs_url_validity) && $whmcs_url_validity == 0) {?>
                    <div class="col-md-12">
                        <label style="text-align: center; color: white; background-color: red"> URL is invalid </label>
                    </div>
                <?php } elseif (isset($whmcs_url_validity) && $whmcs_url_validity == 1) {?>
                    <div class="col-md-12">
                        <label style="text-align: center; color: white; background-color: lightgreen"> URL Valid</label>
                    </div>
                <?php }?>
                <div class="col-md-12">
                    <label></label>
                    <button type="submit" name="<?php echo esc_url($db_whmcs_url) != '' ? 'update_settings' : 'save_settings'?>" class="editor-post-publish-button"><?php echo esc_url($db_whmcs_url) != '' ? 'Update Settings' : 'Save Settings'?></button>
                    <button type="submit" name="test_url" class="editor-post-publish-button">Test URL</button>
                </div>
                <?php
                if (isset($save_response) && $save_response == 'error') {?>
                    <div class="col-md-12">
                        <label style="text-align: center; color: white; background-color: red">Either URL is Empty or Invalid WHMCS URL. Please try Again</label>
                    </div>
                <?php } elseif (isset($save_response) && $save_response == 'success') {?>
                    <div class="col-md-12">
                        <label style="text-align: center; color: white; background-color: lightgreen"> URL Save/Updated Successfully. Please Reload the page Manually</label>
                    </div>
                <?php }?>
            </div>
        </form>
        <span><p><b>Note: After change price in whmcs if you are using cache plugin in your wordpress, for update price you must have to remove cache for posts and pages.</b></p></span>
        <hr>
        <h3><b>Product Attributes(Name, Price & Description)</b></h3>
        <label>How to use Short code in:
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <label>Product Id *</label>
                    <input type="text" name="pid" id="pid" class="pid" placeholder="product id"/>
                </div>
                <div class="col-md-12">
                    <label>Billing Cycle *</label>
                    <select name='cycle' id='cycle' class='cycle'>
                        <option selected value='monthly'>Monthly</option>
                        <option value='yearly'>Annually</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label>Fetching Attribute *</label>
                    <select name='fetcher' id='fetcher' class='fetcher'>
                        <option selected value='price'>Product Price</option>
                        <option value='name'>Product Name</option>
                        <option value='description'>Product Description</option>
                    </select>
                </div>
                <div class="col-md-12 price_unit_div" style='display:none'>
                    <label>Price Unit</label>
                    <select name='price_unit' id='price_unit' class='price_unit'>
                        <option selected value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                    </select>
                    <short>leave as it is if unknown</short>
                </div>
                <div class="col-md-12">
                    <button onclick="Generate_Code_for_Pricing()" class="editor-post-publish-button">Generate</button>
                </div>
                <div class="col-md-12">
                    <span><p id='error_pid' style="color:red; display:none">Please provide Product Id</span>
                </div>
            </div>
        </div>
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <label>Short Code</label>
                    <input type="text" name="short_code" id="short_code" class="short_code" readonly placeholder="[whmcs_details pid='35' bc='monthly' fetch='price' price_unit='1']"/>
                    <button onclick="copy_short_code('short_code')" class="editor-post-publish-button">copy!</button>
                </div>
                <div class="col-md-12">
                    <label>PHP Code</label>
                    <input type="text" name="php_code" id="php_code" class="php_code" readonly placeholder="echo do_shortcode('[whmcs_details pid='35' bc='monthly' fetch='price' price_unit='1']')"/>
                    <button onclick="copy_php_code('php_code')" class="editor-post-publish-button">copy!</button>
                </div>
            </div>
        </div>
        <hr>
        <h3><b>Domain Pricing</b></h3>
        <label>How to use Short code in:
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <label>TLD *</label>
                    <input type="text" name="tld" id="tld" class="tld" placeholder="com, net"/>
                </div>
                <div class="col-md-12">
                    <span><p id='error_tld' style="color:red; display:none">Please provide Domain TLD</span>
                </div>
                <div class="col-md-12">
                    <label>Type *</label>
                    <input type="text" name="type" id="type" class="type" placeholder="register"/>
                </div>
                <div class="col-md-12">
                    <span><p id='error_type' style="color:red; display:none">Please provide Domain Type</span>
                </div>
                <div class="col-md-12">
                    <label>Register *</label>
                    <select name='register' id='register' class='register'>
                        <option selected value=1>1 Year</option>
                        <option value=2>2 Year</option>
                        <option value=3>3 Year</option>
                        <option value=4>4 Year</option>
                        <option value=5>5 Year</option>
                        <option value=6>6 Year</option>
                        <option value=7>7 Year</option>
                        <option value=8>8 Year</option>
                        <option value=9>9 Year</option>
                        <option value=10>10 Year</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label>Price Unit</label>
                    <select name='domai_price_unit' id='domai_price_unit' class='domai_price_unit'>
                        <option selected value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                    </select>
                    <short>leave as it is if unknown</short>
                </div>
                <div class="col-md-12">
                    <button onclick="Generate_Code_for_domain()" class="editor-post-publish-button">Generate</button>
                </div>
            </div>
        </div>
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <label>Short Code</label>
                    <input type="text" name="domain_short_code" id="domain_short_code" class="domain_short_code" readonly placeholder="[whmcs_details tld='com' type='register' register='2m']"/>
                    <button onclick="copy_short_code('domain_short_code')" class="editor-post-publish-button">copy!</button>
                </div>
                <div class="col-md-12">
                    <label>PHP Code</label>
                    <input type="text" name="domain_php_code" id="domain_php_code" class="domain_php_code" readonly placeholder="echo do_shortcode('[whmcs_details tld='com' type='register' register='2y']')"/>
                    <button onclick="copy_php_code('domain_php_code')" class="editor-post-publish-button">copy!</button>
                </div>
            </div>
        </div>
        <hr>
        <h3><b>Domain Pricing Table</b></h3>
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <button onclick="Generate_Code_for_pricing_table()" class="editor-post-publish-button">Generate</button>
                </div>
            </div>
        </div>
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <label>Short Code</label>
                    <input type="text" name="pricing_table_short_code" id="pricing_table_short_code" class="pricing_table_short_code" readonly placeholder="[whmcs_domain_pricing]"/>
                    <button onclick="copy_short_code('pricing_table_short_code')" class="editor-post-publish-button">copy!</button>
                </div>
                <div class="col-md-12">
                    <label>PHP Code</label>
                    <input type="text" name="pricing_table_php_code" id="pricing_table_php_code" class="pricing_table_php_code" readonly placeholder="echo do_shortcode('[whmcs_domain_pricing]')"/>
                    <button onclick="copy_php_code('pricing_table_php_code')" class="editor-post-publish-button">copy!</button>
                </div>
            </div>
        </div>
        <hr>
        <h3><b>Domain Checker</b></h3>
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <button onclick="Generate_Code_for_domain_checker()" class="editor-post-publish-button">Generate</button>
                </div>
            </div>
        </div>
        <div class="settings_elementor_form">
            <div class="row" >
                <div class="col-md-12">
                    <label>Short Code</label>
                    <input type="text" name="domain_checker_short_code" id="domain_checker_short_code" class="domain_checker_short_code" readonly placeholder="[whmcs_domain_pricing]"/>
                    <button onclick="copy_short_code('domain_checker_short_code')" class="editor-post-publish-button">copy!</button>
                </div>
                <div class="col-md-12">
                    <label>PHP Code</label>
                    <input type="text" name="domain_checker_php_code" id="domain_checker_php_code" class="domain_checker_php_code" readonly placeholder="echo do_shortcode('[whmcs_domain_pricing]')"/>
                    <button onclick="copy_php_code('domain_checker_php_code')" class="editor-post-publish-button">copy!</button>
                </div>
            </div>
        </div>
    </div>
</div>
