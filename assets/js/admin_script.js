// Document Ready JS
jQuery( document ).ready(function() {
    let fetch = jQuery('.fetcher').val()?jQuery('.fetcher').val():'price';
    if(fetch == 'price'){
        jQuery('.price_unit_div').show();
    }
});

function Generate_Code_for_Pricing() {
    let pid = jQuery('.pid').val();
    let cycle = jQuery('.cycle').val()?jQuery('.cycle').val():'monthly';
    let fetch = jQuery('.fetcher').val()?jQuery('.fetcher').val():'price';
    let price_unit = jQuery('.price_unit').val()?jQuery('.price_unit').val():1;
    if(pid == ''){
        jQuery('#error_pid').show();
    }else{
        jQuery('#error_pid').hide();
        var short_code_txt = "pid='"+pid+"' bc='"+cycle+"' fetch='"+fetch+"'";
        if(fetch=='price'){
            short_code_txt +=" price_unit='"+price_unit+"'";
        }
        let short_code = "[whmcs_details "+short_code_txt+"]";
        let php_code = 'echo do_shortcode("[whmcs_details '+short_code_txt+']");';
        jQuery('.short_code').val(short_code);
        jQuery('.php_code').val(php_code);
    }
}
function Generate_Code_for_pricing_table() {
    let short_code = "[whmcs_domain_pricing]";
    let php_code = 'echo do_shortcode("[whmcs_domain_pricing]");';
    jQuery('.pricing_table_short_code').val(short_code);
    jQuery('.pricing_table_php_code').val(php_code);
}
function Generate_Code_for_domain_checker() {
    let short_code = "[whmcs_domain_checker]";
    let php_code = 'echo do_shortcode("[whmcs_domain_checker]");';
    jQuery('.domain_checker_short_code').val(short_code);
    jQuery('.domain_checker_php_code').val(php_code);
}

function Generate_Code_for_domain() {
    let tld = jQuery('.tld').val();
    let type = jQuery('.type').val();
    let register = jQuery('.register').val();
    let domain_price_unit = jQuery('.domai_price_unit').val();
    if(tld =='' || type ==''){
        if(tld==''){
            jQuery('#error_tld').show();
        }
        if(type==''){
            jQuery('#error_type').show();
        }
    }else{
        jQuery('#error_type').hide();
        jQuery('#error_tld').hide();
        let domain_short_code_text = "tld='"+tld+"' type='"+type+"' register='"+register+"' format='"+domain_price_unit+"'";
        let short_code = "[whmcs_details "+domain_short_code_text+" ]";
        let php_code = 'echo do_shortcode("[whmcs_details '+domain_short_code_text+']");';
        jQuery('.domain_short_code').val(short_code);
        jQuery('.domain_php_code').val(php_code);
    }
}
// to copy short code of given id element
function copy_short_code(id) {
    console.log('given id: '+id);
    let short_code = document.getElementById(id);
    // Select the text field
    short_code.select();
    document.execCommand("copy");
}
// to copy php code of given id element
function copy_php_code(id) {
    let php_code =document.getElementById(id);
    php_code.select();
    document.execCommand("copy");
}