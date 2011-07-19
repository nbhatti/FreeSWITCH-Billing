/*
  --- menu level scope settins structure --- 
  note that this structure has changed its format since previous version.
  Now this structure has the same layout as Tigra Menu GOLD.
  Format description can be found in product documentation.
*/
var MENU_POS = [
{
	// item sizes
	'height': 24,	//	What's the height of the bar?
	'width': 150, //      How long is the bar itself?
	// menu block offset from the origin:
	//	for root level origin is upper left corner of the page
	//	for other levels origin is upper left corner of parent item
	'block_top': 0,	//	Where should I put the bar? From the top.
	'block_left': 8,	//	Where should I put the bar? From the top.
	// offsets between items of the same level
	'top': 0,		//	What distance (height) should the next box (item) menu  be from the one before?
	'left': 150,		//	What distance (left) should the next box (item) menu  be from the one before?
	// time in milliseconds before menu is hidden after cursor has gone out
	// of any items
	'hide_delay': 0,
	'expd_delay': 0,
	'css' : {
		'outer': ['m0l0oout', 'm0l0oover'],
		'inner': ['m0l0iout', 'm0l0iover']
	}
},
{
	'height': 24,
	'width': 150,
	'block_top': 23,
	'block_left': 0,
	'top': 23,
	'left': 0,
	'css': {
		'outer' : ['m0l1oout', 'm0l1oover'],
		'inner' : ['m0l1iout', 'm0l1iover']
	}
},
{
	'block_top': 0,
	'block_left': 150,
	'css': {
		'outer': ['m0l2oout', 'm0l2oover'],
		'inner': ['m0l1iout', 'm0l2iover']
	}
}
]
