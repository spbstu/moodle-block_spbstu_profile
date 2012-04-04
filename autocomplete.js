function autocomplete(url, input, dropdown) {
    var myDataSource = new YAHOO.util.XHRDataSource(url);
    myDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
    myDataSource.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    myDataSource.maxCacheEntries = 60;
    myDataSource.minQueryLength = 3;

    // Instantiate the AutoComplete
    var myAutoComp = new YAHOO.widget.AutoComplete(input, dropdown, myDataSource);
    document.getElementById(input).style.width = '30%';
    document.getElementById(dropdown).style.width = '30%';
    myAutoComp.allowBrowserAutocomplete = false;
    myAutoComp.maxResultsDisplayed = 20;
    myAutoComp.formatResult = function(oResultData, sQuery, sResultMatch) {
        return (sResultMatch);
    };
/*
    return {
        myDataSource: myDataSource,
        myAutoComp: myAutoComp
    };
*/
}
