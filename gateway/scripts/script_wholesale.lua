--function myHangupHook(s, status, arg)
--     freeswitch.consoleLog("INFO", "CARD INFO: Call ended! " .. status .. "\n")
--     -- close db_conn and terminate
--     cur:close()
--     conn:close()
--     env:close()
--     
--     fsLog("STATUS: ","I'm still working!")
--     error()
--end
--
----  Don't terminate the code when hangup
--session:setHangupHook("myHangupHook", "blah")

--
--  
--        INCLUDE FUNCTIONS
--
dofile "/usr/local/freeswitch/scripts/functions_wholesale.lua"
 
fs_api = freeswitch.API();
--
SetVar("hangup_after_bridge","true")


--        CONNECT TO DB
dbConnect()  

--

--          get some vars
context = GetVar("context")
received_ip = GetVar("sip_received_ip")
caller_id = GetVar("caller_id_number")
called_number = GetVar("destination_number")
fsLog("Context  ", context)


SetVar("called_number",GetVar("destination_number"))
SetVar("caller_id",GetVar("caller_id_number"))
call_uuid = GetVar("uuid")


fsLog("UUID",call_uuid)

--session:setAutoHangup(false)

--reply = fs_api:executeString("uuid_park ".. call_uuid);
--fsLog("WS PARK.....: ".. reply)

fsLog("Called Number", called_number)
fsLog("Received IP  ", received_ip)
fsLog("Context  ", context)


--
--
--        Get Wholesale Customer Settings
--
--

GetCutomerSettings(received_ip)
--fsLog("CUSTOMER OK:",custok)
if custok==false then
     fsLog("CUSTOMER NOT FOUND!","Exiting")

     cur:close()
     conn:close()
     env:close()
     
     session:hangup("NO_ROUTE_DESTINATION");

end

if session:ready() then
     -- Set limit and park
     session:execute("limit","db ${domain} " .. received_ip .." " .. ws_customer_max_calls)
     
     if ws_customer_prepaid==1 and tonumber(ws_customer_balance)<=0 then
          fsLog("CUSTOMER " .. ws_customer_symbol .. " has no money... rejecting","")
     
          cur:close()
          conn:close()
          env:close()
     
          session:hangup("BEARERCAPABILITY_NOTAUTH");
     end
          
     --
     --
     --        Get Wholesale Customer Rate
     --
     --
     
     GetCutomerRate(ws_customer_ratetable)
     --fsLog("CUSTOMER OK:",rateok)
     if rateok==false then
          fsLog("RATE NOT FOUND!","Exiting")
     
          cur:close()
          conn:close()
          env:close()
     
          session:hangup("BEARERCAPABILITY_NOTAUTH");
          
     end     
     
     --
     --
     --     Get destination
     --
     --
     
     GetRoute(rate_gateway)
     if gwok==false then 
          session:hangup("DESTINATION_OUT_OF_ORDER");
          return 
     end  
     
     --
     --
     --     Get Cost
     --
     --
     
     --GetProviderCost()
     --fsLog("COST OK:",costok)
     
     
     --
     --
     --     Calculate duration and say it
     --
     --
     
     CalcDuration()
     
     --
     --
     --     Send out the call
     --
     --
     
     --if strip_digits==nil then
     --     strip_digits=""
     --end
     --
     --if out_prefix==nil then
     --     out_prefix=""
     --end
     -- 
     ----out_number= string.gsub(called_number, "^".. gw_strip_digits, gw_out_prefix)
     out_number=string.gsub(called_number, "^00" , "")
     
     fsLog("OUT NUMBER: ",out_number)
     fsLog("SIP IP: ",route_name)
     
     -- SET NIBBLE PER MINUTE RATE && ACCOUNT TO DEDUCT
     --session:execute("set","nibble_rate=" .. rate_rate )
     --session:execute("set","nibble_account=" .. ws_customer_id )
     --session:execute("nibblebill","heartbeat 1")
     session:execute("set","hangup_after_bridge=true")
     
     
      
     session:execute("set","effective_caller_id_number=" .. caller_id)
     session:execute("set","continue_on_fail=NORMAL_TEMPORARY_FAILURE,TIMEOUT,NO_ROUTE_DESTINATION,UNALLOCATED_NUMBER,407")

     --session:execute("set","proxy_media=true")
     session:execute("set","bypass_media=false")
     
     
     --session:execute("bridge","{sip_auth_username=" .. gw_sip_username .. ",sip_auth_password=" .. gw_sip_pwd .. "}sofia/external/".. out_number .."@".. gw_sip_ip .."")

     session:execute("export","nolocal:absolute_codec_string=G729")
     
     fsLog("BRIDGE EXECUTE:", "{loop=3}sofia/gateway/${distributor(" .. route_name .. ")}" .. out_number .. "")
     session:execute("bridge","{loop=3}sofia/gateway/${distributor(" .. route_name .. ")}".. out_number .."")
     
     -- hangup
     session:hangup();
     
     --if session:answered() then 
     --     fsLog("CALL STATUS", "CALL CONNECTED!" )
     --end
     
     
     --sched_hangup [+]<time> <uuid> [<cause>]  
     
     
     -- close everything                

end

cur:close()
conn:close()
env:close()


