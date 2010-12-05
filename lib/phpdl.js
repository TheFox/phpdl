
/*
	Created @ 05.12.2010 by TheFox@fox21.at
	Copyright (c) 2010 TheFox
	
	This file is part of PHPDL.
	
	PHPDL is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	PHPDL is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with PHPDL.  If not, see <http://www.gnu.org/licenses/>.
*/


$(document).ready(function(){
	
	// Beauty Tips
	$('#ctimeHelp').bt('Creation time.', { trigger: 'hover', positions: 'top' });
	$('#stimeHelp').bt('Start time.', { trigger: 'hover', positions: 'top' });
	$('#ftimeHelp').bt('Finish time.', { trigger: 'hover', positions: 'top' });
	$('#exportHelp').bt('Export the packet as XML or TXT.', { trigger: 'hover', positions: 'top' });
	$('#archiveHelp').bt('Move a packet to the archive.', { trigger: 'hover', positions: 'top' });
	
});

function packetErrorsTip(obj, text){
	$(obj).bt(text, { trigger: 'hover', positions: 'top' });
	$(obj).btOn();
	$(obj).removeAttr('onMouseOver');
}

// EOF
