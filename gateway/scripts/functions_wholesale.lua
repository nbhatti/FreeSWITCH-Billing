-----------------------------------------------
-----------------------------------------------

function dbConnect()
     -- connect to db
     require "luasql.mysql"
     env = assert(luasql.mysql())
     conn = assert(env:connect("viking","viking","V1k1ng","192.168.168.2"))


     -- Check whether we should log to db
     context = GetVar("context")
     received_ip = GetVar("sip_received_ip")
     called_number = GetVar("destination_number")
     
     curlog_on = assert(
          conn:execute( "select running_trace from ws_settings where remote_host='".. received_ip .."' and service_type='".. context .."' and '".. called_number .."' like concat(dialed_number,'%');" )
     )
     trace_on = curlog_on:fetch ({}, "a")
     
     --   If null, no trace is enabled.
     
     if trace_on == nil then
          log_on = true
          return     
     else
          log_on = true
          return     
     end     
     

end

-----------------------------------------------
-----------------------------------------------

function fsLog(name,dta)
     freeswitch.consoleLog("info", "WS CALL ".. name ..".....: ".. dta .."\n");

     if log_on then     
          return     
     else
          curlog = assert(
               conn:execute( "insert wholesale_app_log values (null,now(),'".. received_ip .."','".. context .."','".. called_number .."','".. name ..": ".. dta .."');" )
          )          
     end
end

-----------------------------------------------
-----------------------------------------------

function GetVar(var)
     return session:getVariable(var)
end

-----------------------------------------------
-----------------------------------------------

function SetVar(var1,value)
     session:setVariable(var1,value)
end

-----------------------------------------------
-----------------------------------------------

function schedule_exec()
     fsLog("SCHEDULER","TEST!")
end

-----------------------------------------------
-----------------------------------------------

function GetCutomerSettings(ip)

          custok=false

          cur = assert( 
               conn:execute( "select ws_customer_id, ws_customer_company, ws_customer_symbol, ws_customer_sig_ip, ws_customer_ratetable, ws_customer_prepaid, ws_customer_balance, ws_customer_enabled, ws_customer_max_calls from ws_customers where ws_customer_sig_ip ='".. ip .."' and ws_customer_context='" .. context .. "' and ws_customer_enabled=1;" )
               )
               
          -- print all rows, the rows will be indexed by field names
          row_cust = cur:fetch ({}, "a")
          
          --   If null, no customer was found.
          
          if row_cust == nil then

               return
               
          else
               fsLog("Results","OK") 
     
               while row_cust do

                    fsLog("Cust ID",                row_cust.ws_customer_id                 )
                    fsLog("Company",                row_cust.ws_customer_company            )
                    fsLog("Symbol",                 row_cust.ws_customer_symbol             )
                    fsLog("Signalling IP",          row_cust.ws_customer_sig_ip             )
                    fsLog("Rate Table",             row_cust.ws_customer_ratetable          )
                    fsLog("Prepaid?",               row_cust.ws_customer_prepaid            )
                    fsLog("Balance",                row_cust.ws_customer_balance            )
                    fsLog("Enabled",                row_cust.ws_customer_enabled            )
                    fsLog("Maximum calls allowed",  row_cust.ws_customer_max_calls          )
     
                    ws_customer_id             = row_cust.ws_customer_id
                    ws_customer_company             = row_cust.ws_customer_company        
                    ws_customer_symbol              = row_cust.ws_customer_symbol        
                    ws_customer_sig_ip              = row_cust.ws_customer_sig_ip        
                    ws_customer_ratetable           = row_cust.ws_customer_ratetable        
                    ws_customer_prepaid             = row_cust.ws_customer_prepaid        
                    ws_customer_balance             = row_cust.ws_customer_balance        
                    ws_customer_enabled             = row_cust.ws_customer_enabled        
                    ws_customer_max_calls           = row_cust.ws_customer_max_calls        

                    SetVar("ws_customer_id"         ,ws_customer_id                                   )     
                    SetVar("ws_customer_company"    ,ws_customer_company                              )
                    SetVar("ws_customer_symbol"     ,ws_customer_symbol                               )
                    SetVar("ws_customer_sig_ip"     ,ws_customer_sig_ip                               )
                    SetVar("ws_customer_ratetable"  ,ws_customer_ratetable                            )
                    SetVar("ws_customer_prepaid"    ,ws_customer_prepaid                              )
                    SetVar("ws_customer_balance"    ,ws_customer_balance                              )
                    SetVar("ws_customer_enabled"    ,ws_customer_enabled                              )
                    SetVar("ws_customer_max_calls"  ,ws_customer_max_calls                            )
     
                    
                    custok=true
               
                 -- reusing the table of results
                 row_cust = cur:fetch (row_cust, "a")
               end
     

          end
end

-----------------------------------------------
-----------------------------------------------

-----------------------------------------------
-----------------------------------------------

function GetCutomerRate(ratefile)

          rateok=false
          cur = assert( 
               conn:execute( "select * from " .. ratefile .. " where '" .. called_number .. "' like concat(areacode,'%') order by length(areacode) desc limit 1;" )
               )
               
          -- print all rows, the rows will be indexed by field names
          row_rate = cur:fetch ({}, "a")
          
          --   If null, no customer was found.
          
          if row_rate == nil then

               return
               
          else
               fsLog("Rate","OK") 
     
               while row_rate do
     
                    fsLog("areacode",               row_rate.areacode         )
                    fsLog("description",            row_rate.description      )
                    fsLog("rate",                   row_rate.rate             )
                    fsLog("Gateway",                row_rate.route          )
     
                    rate_areacode                   = row_rate.areacode        
                    rate_description                = row_rate.description        
                    rate_rate                       = row_rate.rate        
                    rate_gateway                    = row_rate.route        
     
                    SetVar("rate_areacode"          ,rate_areacode            )
                    SetVar("rate_description"       ,rate_description         )
                    SetVar("rate_rate"              ,rate_rate                )
                    SetVar("rate_gateway"           ,rate_gateway             )
                    
                    rateok=true
               
                 -- reusing the table of results
                 row_rate = cur:fetch (row_rate, "a")
               end
     

          end
end

-----------------------------------------------
-----------------------------------------------

-----------------------------------------------
-----------------------------------------------

function GetGateWay(provider)

          gwok=false
          --fsLog("SQL: ","select symbol, sip_ip, strip_digits, out_prefix, cost_table, sip_username, sip_pwd from ws_providers where symbol = \'" .. provider .. "\';" )
          cur = assert( 
               conn:execute( "select symbol, sip_ip, strip_digits, out_prefix, cost_table, sip_username, sip_pwd from ws_providers where symbol = '" .. provider .. "';" )
               )
               
          -- print all rows, the rows will be indexed by field names
          row_gw = cur:fetch ({}, "a")
          
          --   If null, no customer was found.
          
          if row_gw == nil then

               return
               
          else
               fsLog("Gateway","OK") 
     
               while row_gw do
     
                    fsLog("gw_symbol",             row_gw.symbol              )
                    fsLog("gw_sip_ip",             row_gw.sip_ip              )
                    fsLog("gw_strip_digits",       row_gw.strip_digits        )
                    fsLog("gw_out_prefix",         row_gw.out_prefix          )
                    fsLog("gw_cost_table",         row_gw.cost_table          )
                    fsLog("gw_sip_username",       row_gw.sip_username        )
                    fsLog("gw_sip_pwd",            row_gw.sip_pwd             )
     
                    gw_symbol                      = row_gw.symbol        
                    gw_sip_ip                      = row_gw.sip_ip
                    gw_strip_digits                = row_gw.strip_digits
                    gw_out_prefix                  = row_gw.out_prefix
                    gw_cost_table                  = row_gw.cost_table
                    gw_sip_username                = row_gw.sip_username
                    gw_sip_pwd                     = row_gw.sip_pwd
     
                    SetVar("gw_symbol"             ,gw_symbol                 )
                    SetVar("gw_sip_ip"             ,gw_sip_ip                 )
                    SetVar("gw_strip_digits"       ,gw_strip_digits           )
                    SetVar("gw_out_prefix"         ,gw_out_prefix             )
                    SetVar("gw_cost_table"         ,gw_cost_table             )
                    SetVar("gw_sip_username"       ,gw_sip_username           )
                    SetVar("gw_sip_pwd"            ,gw_sip_pwd                )
                    
                    gwok=true
               
                 -- reusing the table of results
                 row_gw = cur:fetch (row_gw, "a")
               end
          end
end

-----------------------------------------------
-----------------------------------------------


function GetProviderCost()

          costok=false
          --fsLog("SQL: ","select * from " .. gw_cost_table .. " where \'" .. called_number .. "\' like concat(areacode,\'%\') order by length(areacode) desc limit 1;")
          cur = assert( 
               conn:execute( "select * from " .. gw_cost_table .. " where '" .. called_number .. "' like concat(areacode,'%') order by length(areacode) desc limit 1;" )
               )
               
          -- print all rows, the rows will be indexed by field names
          row_cost = cur:fetch ({}, "a")
          
          --   If null, no customer was found.
          
          if row_cost == nil then

               return
               
          else
               fsLog("Rate","OK") 
     
               while row_cost do
     
                    fsLog("areacode",               row_cost.areacode         )
                    fsLog("description",            row_cost.description      )
                    fsLog("rate",                   row_cost.cost             )
     
                    cost_areacode                   = row_cost.areacode        
                    cost_description                = row_cost.description        
                    cost_rate                       = row_cost.cost        
     
                    SetVar("cost_areacode"          ,cost_areacode            )
                    SetVar("cost_description"       ,cost_description         )
                    SetVar("cost_rate"              ,cost_rate                )
                    
                    costok=true
               
                 -- reusing the table of results
                 row_cost = cur:fetch (row_cost, "a")
               end
     

          end
end

-----------------------------------------------
-----------------------------------------------


function GetRoute()

          costok=false
          --fsLog("SQL: ","select * from ws_routes where route_name = \'" .. rate_gateway .. "\';")
          cur = assert( 
               conn:execute( "select * from ws_routes where route_name = '" .. rate_gateway .. "';" )
               )
               
          -- print all rows, the rows will be indexed by field names
          row_route = cur:fetch ({}, "a")
          
          --   If null, no customer was found.
          
          if row_route == nil then

               return
               
          else
               fsLog("Rate","OK") 
     
               while row_route do
     
                    fsLog("route_name",                     row_route.route_name          )

                    fsLog("route_gw_1_symbol",              row_route.route_gw_1_symbol   )
                    fsLog("route_gw_1_max_chan",            row_route.route_gw_1_max_chan )
                    fsLog("route_gw_1_weight",              row_route.route_gw_1_weight   )
     
                    fsLog("route_gw_2_symbol",              row_route.route_gw_2_symbol   )
                    fsLog("route_gw_2_max_chan",            row_route.route_gw_2_max_chan )
                    fsLog("route_gw_2_weight",              row_route.route_gw_2_weight   )
     
                    fsLog("route_gw_3_symbol",              row_route.route_gw_3_symbol   )
                    fsLog("route_gw_3_max_chan",            row_route.route_gw_3_max_chan )
                    fsLog("route_gw_3_weight",              row_route.route_gw_3_weight   )
     
                    fsLog("route_gw_4_symbol",              row_route.route_gw_4_symbol   )
                    fsLog("route_gw_4_max_chan",            row_route.route_gw_4_max_chan )
                    fsLog("route_gw_4_weight",              row_route.route_gw_4_weight   )
     
                    fsLog("route_gw_5_symbol",              row_route.route_gw_5_symbol   )
                    fsLog("route_gw_5_max_chan",            row_route.route_gw_5_max_chan )
                    fsLog("route_gw_5_weight",              row_route.route_gw_5_weight   )
 
                                                               
 
                    route_name                              = row_route.route_name         
                                                                                         
                    route_gw_1_symbol                       = row_route.route_gw_1_symbol  
                    route_gw_1_max_chan                     = row_route.route_gw_1_max_chan
                    route_gw_1_weight                       = row_route.route_gw_1_weight 
                                                                                         
                    route_gw_2_symbol                       = row_route.route_gw_2_symbol  
                    route_gw_2_max_chan                     = row_route.route_gw_2_max_chan
                    route_gw_2_weight                       = row_route.route_gw_2_weight 
                                                                                         
                    route_gw_3_symbol                       = row_route.route_gw_3_symbol  
                    route_gw_3_max_chan                     = row_route.route_gw_3_max_chan
                    route_gw_3_weight                       = row_route.route_gw_3_weight 
                                                                                         
                    route_gw_4_symbol                       = row_route.route_gw_4_symbol  
                    route_gw_4_max_chan                     = row_route.route_gw_4_max_chan
                    route_gw_4_weight                       = row_route.route_gw_4_weight 
                                                                                         
                    route_gw_5_symbol                       = row_route.route_gw_5_symbol  
                    route_gw_5_max_chan                     = row_route.route_gw_5_max_chan
                    route_gw_5_weight                       = row_route.route_gw_5_weight
 
     
                    SetVar("route_name"                      , route_name )              
                                            
                    SetVar("route_gw_1_symbol"               , route_gw_1_symbol              )
                    SetVar("route_gw_1_max_chan"             , route_gw_1_max_chan            )
                    SetVar("route_gw_1_weight"               , route_gw_1_weight              )

                    SetVar("route_gw_2_symbol"               , route_gw_2_symbol              )
                    SetVar("route_gw_2_max_chan"             , route_gw_2_max_chan            )
                    SetVar("route_gw_2_weight"                , route_gw_2_weight             ) 

                    SetVar("route_gw_3_symbol"                , route_gw_3_symbol             ) 
                    SetVar("route_gw_3_max_chan"              , route_gw_3_max_chan           ) 
                    SetVar("route_gw_3_weight"                , route_gw_3_weight             ) 

                    SetVar("route_gw_4_symbol"                , route_gw_4_symbol             ) 
                    SetVar("route_gw_4_max_chan"              , route_gw_4_max_chan           ) 
                    SetVar("route_gw_4_weight"                , route_gw_4_weight             ) 

                    SetVar("route_gw_5_symbol"                , route_gw_5_symbol             ) 
                    SetVar("route_gw_5_max_chan"              , route_gw_5_max_chan           ) 
                    SetVar("route_gw_5_weight"                , route_gw_5_weight             ) 
                    
                   
                    routeok=true
               
                 -- reusing the table of results
                 row_route = cur:fetch (row_route, "a")
               end
     

          end
end

-----------------------------------------------
-----------------------------------------------


function SayBalance()
     session:streamFile(card_balance_audio_file)
     session:execute("say","en number pronounced "..math.floor(balance))
     session:streamFile(card_currency_audio_file)
     session:execute("say","en number pronounced ".. (balance - math.floor(balance))*100 )
     session:streamFile(card_currency_decimal_audio_file)
end


-----------------------------------------------
-----------------------------------------------

function CalcDuration()
     per_sec = rate_rate/60
     max_call_dura = math.floor(ws_customer_balance/rate_rate)
     SetVar("max_call_dura", max_call_dura )
end

-----------------------------------------------
-----------------------------------------------


function EndCall()
     cur:close()
     conn:close()
     env:close()

     session:hangup(404)
end
