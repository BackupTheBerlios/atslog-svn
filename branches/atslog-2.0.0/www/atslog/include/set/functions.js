<script type="text/javascript" language="javascript">
<!--

var marked_row = new Array;

function setPointer(theRow, theRowNum, theAction)
{
    var theCells = null;
    var theDefaultColor ='<?php echo $COLORS['TrOnmouseOne']; ?>';
    var thePointerColor='<?php echo $COLORS['TrOnmouseTwo']; ?>';
    var theMarkColor='<?php echo $COLORS['TrOnmouseThree']; ?>';

    // 1. Pointer and mark feature are disabled or the browser can't get the
    //    row -> exits
    if ((thePointerColor == '' && theMarkColor == '')
        || typeof(theRow.style) == 'undefined') {
        return false;
    }

    // 2. Gets the current row and exits if the browser can't get it
    if (typeof(document.getElementsByTagname) != 'undefined') {
        theCells = theRow.getElementsByTagname('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }

    // 3. Gets the current color...
    var rowCellsCnt  = theCells.length;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    // 3.1 ... with DOM compatible browsers except Opera that does not return
    //         valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        domDetect    = true;
    }
    // 3.2 ... with other browsers
    else {
        currentColor = theCells[0].style.backgroundColor;
        domDetect    = false;
    } // end 3

    // 3.3 ... Opera changes colors set via HTML to rgb(r,g,b) format so fix it
    if (currentColor.indexOf("rgb") >= 0) 
    {
        var rgbStr = currentColor.slice(currentColor.indexOf('(') + 1,
                                     currentColor.indexOf(')'));
        var rgbValues = rgbStr.split(",");
        currentColor = "#";
        var hexChars = "0123456789ABCDEF";
        for (var i = 0; i < 3; i++)
        {
            var v = rgbValues[i].valueOf();
            currentColor += hexChars.charAt(v/16) + hexChars.charAt(v%16);
        }
    }

    // 4. Defines the new color
    // 4.1 Current color is the default one
    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.2 Current color is the pointer one
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.3 Current color is the marker one
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
        }
    } // end 4

    // 5. Sets the new color...
    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].setAttribute('bgcolor', newColor, 0);
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
} // end of the 'setPointer()' function



/* ������������� ������� �������� ������ � �������.  */
opera = (navigator.userAgent.indexOf('Opera') >= 0)? true : false;
ie = (document.all && !opera)? true : false;
dom = (document.getElementById && !ie && !opera)? true : false;
treeOpen = new Array();
treeItems = new Array();
treeOpen[740] = false;
treeItems[740] = new Array('t740','i740');
treeOpen[740] = false;
treeItems[739] = new Array('t739','i739');
function changeTree(which) {
	if (treeOpen[which]) {
		removeElem(treeItems[which][0]);
		putElem(treeItems[which][1],"table-row");
		treeOpen[which] = false;
	} else {
		removeElem(treeItems[which][1]);
		putElem(treeItems[which][0],"table-row");
		treeOpen[which] = true;
	}
}
function putElem(elemId,displayValue) {
if (dom) document.getElementById(elemId).style.display = (displayValue)? displayValue : "block";
else if (ie) document.all[elemId].style.display = "block";
}
function removeElem(elemId) {
if (dom) document.getElementById(elemId).style.display = "none";
else if (ie) document.all[elemId].style.display = "none";
}
if (dom || ie) {
document.writeln('<style type="text/css">');
document.writeln('.treeElem \{ display: none; \}');
document.writeln("<\/style>");
}

// -->
</script>
