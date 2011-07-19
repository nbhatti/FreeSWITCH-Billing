/*
  --- menu items --- 
  note that this structure has changed its format since previous version.
  additional third parameter is added for item scope settings.
  Now this structure is compatible with Tigra Menu GOLD.
  Format description can be found in product documentation.
*/
var MENU_ITEMS = [
     ['Incomming',null,null,
          ['VoIP',null,null,
          	['View','view_voipin.php'],
          	['Create','create_voipin.php'],
          ],
          ['TDM',null,null,
          	['View','view_tdmin.php'],
          	['Create','create_tdmin.php'],
          ],
     ],
     ['Outgoing',null,null,
          ['VoIP',null,null,
          	['View','view_voipout.php'],
          	['Create','create_voipout.php'],
          ],
          ['TDM',null,null,
          	['View','view_tdmout.php'],
          	['Create','create_tdmout.php'],
          ],
     ],
	['Routing', null, null,
		['View DNO Definitions', 'create_dno.php'],
		['Routes',null,null,
			['View','view_routes.php'],
			['Create','create_route.php'],
		],
		['View Routings', 'routings.php'],
	],
/*	
	['Special', null, null,
		['View Range ASR', 'GetInfoRange.php'],
		['Yesterday',null,null,
			['Customer','LastHour.php?scope=date&direction=in&date=yesterday'],
			['Provider','LastHour.php?scope=date&direction=out&date=yesterday'],
			['Destinations','LastHour.php?scope=date&direction=destination&date=yesterday'],
		],
		['View Weekly Traffic','WeeklyData.php'],
	],
	['Notifications', null, null,
		['View users', 'NotificationUsers.php'],
        ],
        ['User & system admin', null, null,
                ['Create new user', 'ASRNewUser.php'],
                ['View system users', 'ViewSystemUsers.php'],
                ['Parameters','NotificationParam.php'],
        ],
        ['Informacion', null, null,
                ['Estado actual', 'telesmonitor.php'],
                ['Saturacion Teles Span', 'telessat.php'],
                ['Saturacion Teles Owner', 'telessatowner.php'],
        ],
        //['Excel', null, null,
                ['Excel', 'http://asr.opencall.es/excel',
        //        ['Acumulado del dia', 'LastWholeExcel.php'],
        //        ['Ayer', 'LastDayExcel.php'],
        ]
*/
];

