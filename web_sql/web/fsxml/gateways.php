<?php

require "../webint/conexion.inc";

echo "
<document type=\"freeswitch/xml\">
  <section name=\"configuration\">

     <configuration name=\"sofia.conf\" description=\"sofia Endpoint\">
     
       <global_settings>
         <param name=\"log-level\" value=\"0\"/>
         <!-- <param name=\"auto-restart\" value=\"false\"/> -->
         <param name=\"debug-presence\" value=\"0\"/>
       </global_settings>


            <profiles>
";

$resultado_profiles = mysql_query("select service_ip, service_port, service_type from ws_settings") or die("La consulta ha fallado;: " . mysql_error());
while($linea_profile=mysql_fetch_row($resultado_profiles)){

     echo "     
                 <profile name=\"$linea_profile[2]\">
                      <!-- http://wiki.freeswitch.org/wiki/Sofia_Configuration_Files --> 
                      <!-- This profile is only for outbound registrations to providers -->
                      <gateways>
                    
          ";
          
               $resultado = mysql_query("select symbol, sip_ip, out_prefix, sip_username, sip_pwd from ws_providers order by symbol") or die("La consulta ha fallado;: " . mysql_error());
               while($linea=mysql_fetch_row($resultado)){
                    echo "
                         <gateway name=\"$linea[0]\">
                              <param name=\"realm\" value=\"$linea[1]\"/>
                              <param name=\"username\" value=\"$linea[3]\"/>
                              <param name=\"password\" value=\"$linea[4]\"/>
                              <param name=\"register\" value=\"false\"/>
                              <param name=\"retry-seconds\" value=\"30\"/>
                         </gateway>
                    ";
               }
          
          echo "          
                      </gateways>
                    
                      <aliases>
                        <!-- 
                        <alias name=\"outbound\"/>
                        <alias name=\"nat\"/>
                        -->
                      </aliases>
                    
                      <domains>
                        <domain name=\"all\" alias=\"false\" parse=\"true\"/>
                      </domains>
                    
                      <settings>
                        <param name=\"debug\" value=\"0\"/>
                    	<!-- If you want FreeSWITCH to shutdown if this profile fails to load, uncomment the next line. -->
                    	<!-- <param name=\"shutdown-on-fail\" value=\"true\"/> -->
                        <param name=\"sip-trace\" value=\"no\"/>
                        <param name=\"rfc2833-pt\" value=\"101\"/>
                        <param name=\"sip-port\" value=\"$linea_profile[1]\"/>
                    <!--    <param name=\"sip-port\" value=\"5061\"/>                         -->
                        <param name=\"dialplan\" value=\"XML\"/> 
                        <param name=\"context\" value=\"$linea_profile[2]\"/>
                        <param name=\"dtmf-duration\" value=\"2000\"/>
                        <param name=\"inbound-codec-prefs\" value=\"\$\${global_codec_prefs}\"/>
                        <param name=\"outbound-codec-prefs\" value=\"\$\${outbound_codec_prefs}\"/>
                        <param name=\"hold-music\" value=\"\$\${hold_music}\"/>
                        <param name=\"rtp-timer-name\" value=\"soft\"/>
                        <!--<param name=\"enable-100rel\" value=\"true\"/>-->
                        <!-- This could be set to \"passive\" -->
                        <param name=\"local-network-acl\" value=\"localnet.auto\"/>
                        <param name=\"manage-presence\" value=\"false\"/>
                    
                        <!-- used to share presence info across sofia profiles 
                    	 manage-presence needs to be set to passive on this profile
                    	 if you want it to behave as if it were the internal profile 
                    	 for presence.
                        -->
                        <!-- Name of the db to use for this profile -->
                        <!--<param name=\"dbname\" value=\"share_presence\"/>-->
                        <!--<param name=\"presence-hosts\" value=\"\$\${domain}\"/>-->
                        <!--<param name=\"force-register-domain\" value=\"\$\${domain}\"/>-->
                        <!--all inbound reg will stored in the db using this domain -->
                        <!--<param name=\"force-register-db-domain\" value=\"\$\${domain}\"/>-->
                        <!-- ************************************************* -->
                    
                        <!--<param name=\"aggressive-nat-detection\" value=\"true\"/>-->
                        <param name=\"inbound-codec-negotiation\" value=\"generous\"/>
                        <param name=\"nonce-ttl\" value=\"60\"/>
                        <param name=\"auth-calls\" value=\"false\"/>
                        <!--
                    	DO NOT USE HOSTNAMES, ONLY IP ADDRESSES IN THESE SETTINGS!
                        -->
                        <param name=\"rtp-ip\" value=\"$linea_profile[0]\"/>
                        <param name=\"sip-ip\" value=\"$linea_profile[0]\"/>
                        <param name=\"ext-rtp-ip\" value=\"auto-nat\"/>
                        <param name=\"ext-sip-ip\" value=\"auto-nat\"/>
                        <param name=\"rtp-timeout-sec\" value=\"300\"/>
                        <param name=\"rtp-hold-timeout-sec\" value=\"1800\"/>
                        <!--<param name=\"enable-3pcc\" value=\"true\"/>-->
                    
                        <!-- TLS: disabled by default, set to \"true\" to enable -->
                        <param name=\"tls\" value=\"\$\${external_ssl_enable}\"/>
                        <!-- additional bind parameters for TLS -->
                        <param name=\"tls-bind-params\" value=\"transport=tls\"/>
                        <!-- Port to listen on for TLS requests. (5081 will be used if unspecified) -->
                        <param name=\"tls-sip-port\" value=\"\$\${external_tls_port}\"/>
                        <!-- Location of the agent.pem and cafile.pem ssl certificates (needed for TLS server) -->
                        <param name=\"tls-cert-dir\" value=\"\$\${external_ssl_dir}\"/>
                        <!-- TLS version (\"sslv23\" (default), \"tlsv1\"). NOTE: Phones may not work with TLSv1 -->
                        <param name=\"tls-version\" value=\"\$\${sip_tls_version}\"/>
                    
                      </settings>
                    </profile>
";

}

echo "     
       </profiles>
     
     </configuration>

";

echo " 
     <configuration name=\"distributor.conf\" description=\"Distributor Configuration\">
       <lists>
         <!-- every 10 calls to test you will get foo1 once and foo2 9 times...yes NINE TIMES! -->
         <!-- this is not the same as 100 with 10 and 90 that would do foo1 10 times in a row then foo2 90 times in a row -->
";

     $resultado = mysql_query("select route_name, route_gw_1_symbol, route_gw_1_weight, route_gw_2_symbol, route_gw_2_weight, route_gw_3_symbol, route_gw_3_weight, route_gw_4_symbol, route_gw_4_weight, route_gw_5_symbol, route_gw_5_weight, route_gw_1_weight+route_gw_2_weight+route_gw_3_weight+route_gw_4_weight+route_gw_5_weight as total_weight from ws_routes") or die("La consulta ha fallado;: " . mysql_error());
     while($linea=mysql_fetch_row($resultado)){
          echo "             <list name=\"$linea[0]\" total-weight=\"$linea[11]\">\n";
          if( $linea[1]>''  ){ echo "                <node name=\"$linea[1]/\" weight=\"$linea[2]\"/>\n"; }
          if( $linea[3]>''  ){ echo "                <node name=\"$linea[3]/\" weight=\"$linea[4]\"/>\n"; }
          if( $linea[5]>''  ){ echo "                <node name=\"$linea[5]/\" weight=\"$linea[6]\"/>\n"; }
          if( $linea[7]>''  ){ echo "                <node name=\"$linea[7]/\" weight=\"$linea[8]\"/>\n"; }
          if( $linea[9]>''  ){ echo "                <node name=\"$linea[9]/\" weight=\"$linea[10]\"/>\n"; }
          echo "            </list>\n";
     }

echo "
       </lists>
     </configuration>
  </section>


     <section name=\"dialplan\">
";

$resultado_profiles = mysql_query("select service_ip, service_port, service_type from ws_settings") or die("La consulta ha fallado;: " . mysql_error());
while($linea_profile=mysql_fetch_row($resultado_profiles)){

     echo "
       
          <context name=\"$linea_profile[2]\">
          
               <extension name=\"unloop\">
                    <condition field=\"\$\${unroll_loops}\" expression=\"^true$\"/>
                    <condition field=\"\$\${sip_looped_call}\" expression=\"^true$\">
                         <action application=\"deflect\" data=\"\$\${destination_number}\"/>
                    </condition>
               </extension>
               
               <extension name=\"outside_call\" continue=\"true\">
                    <condition>
                         <action application=\"set\" data=\"outside_call=true\"/>
                    </condition>
               </extension>
               
               <extension name=\"hangup\">
                    <condition field=\"destination_number\" expression=\"^(hangup)\$\">
                         <action application=\"hangup\"/>
                    </condition>
               </extension>
               
               <extension name=\"wholesale\">
                    <condition field=\"destination_number\" expression=\"^.*\$\">
                         <action application=\"info\"/>
                         <action application=\"set\" data=\"hangup_after_bridge=true\"/>
                         <action application=\"lua\" data=\"/usr/local/freeswitch/scripts/script_wholesale.lua\"/>
                    </condition>
               </extension>
          
          
          </context>
     ";
}

echo "
     </section>

</document>
";

?>

