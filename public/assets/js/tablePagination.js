

$( document ).ready(function() {

    var table = $('#individualsTable').DataTable();
    //   new $.fn.dataTable.FixedHeader( table );

    // Setup - add a text input to each footer cell
    $('#isolatesTable tfoot th').each( function () {
        var title = $('#isolatesTable thead th').eq( $(this).index() ).text();
        if (title !="Edit" && title!="Status" && title!="Details"){
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        }
    });



    // Apply the search
    table.columns().eq( 0 ).each( function ( colIdx ) {
        $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
            table
                .column( colIdx )
                .search( this.value )
                .draw();
        } );
    } );
    $("#checkbox").attr( "class", "a" );
    $("#status").attr( "class", "a" );
    $("#more").attr( "class", "a" );
    $(".srch").hide();


})



function toggleSearch(){
    if ($('#srchR').is(':visible')){
        $('#srchR').hide();
        $("#advancedSearchA").html('<i class="fa fa-search" id="faAvancedSearch"></i>&nbsp;Advanced Search');
        //$(elem).html('<a href="#"><i class="fa fa-search"></i>search</a>');
    }
    else{
        $('#srchR').show();
        $("#advancedSearchA").html('<i class="fa fa-search" id="faAvancedSearch"></i>&nbsp;Hide Advanced Search');
        // $(elem).html('<a href="#"><i class="fa fa-search"></i>hide</a>');
    }
}

//setTimeout(doSomething, 1000);

function setTableToggle() {
    var url = document.URL;
    if (url.indexOf("Full")>0){
        $("#toggleTableA").html('&nbsp;&nbsp;&nbsp;show condensed data fields');
    }
    else {
        $("#toggleTableA").html('&nbsp;&nbsp;&nbsp;show all data fields');
    }
}



function toggleTable(){
    var url = document.URL;
    if (url.indexOf("Full")>0){
        window.location.replace("/isolates");
    }
    else{
        window.location.replace("/isolatesFull");
    }
}


$(function() {

    $("table:first").tablesorter({
        theme : 'blue',
        // initialize zebra striping and resizable widgets on the table
        widgets: [ "zebra", "resizable" ],
        widgetOptions: {
            resizable_addLastColumn : true
        }
    });

    $("table:last").tablesorter({
        theme : 'blue',
        // initialize zebra striping and resizable widgets on the table
        widgets: [ "zebra", "resizable" ],
        widgetOptions: {
            resizable: true,
            // These are the default column widths which are used when the table is
            // initialized or resizing is reset; note that the "Age" column is not
            // resizable, but the width can still be set to 40px here
            resizable_widths : [ '10%', '10%', '40px', '10%', '100px' ]
        }
    });

});
var all=0;  //add listeners the first time there's a click; after, do nothing. all actions are taken by the listeners




function activateTableSelections(){
    if (all==0){
        //this is the first time "all" is clicked, so do add all to the checkedRowsSet array
        $(":checkbox").parent().parent().children().find(":checkbox").prop('checked', true);
        if($("#all").is(":checked")) {
            $(":checkbox").prop('checked', true)
            $(":checkbox").each(function(e,v){

                var index = checkedRowsSet.indexOf(v.id);

                if (index<0) {
                    checkedRowsSet.push(v.id)
                }
            })
            $("#submitAddExtractSampleIds").val(checkedRowsSet.join());
            console.log($("#submitAddExtractSampleIds").val())
            console.log(checkedRowsSet.join())
        }
        //add a listener for future clicks
        $("#all").on("click", function(){
            if($("#all").is(":checked")) {
                $(":checkbox").prop('checked', true)
                $(":checkbox").each(function(e,v){

                    var index = checkedRowsSet.indexOf(v.id);

                    if (index<0) {
                        checkedRowsSet.push(v.id)
                    }
                })
                $("#submitAddExtractSampleIds").val(checkedRowsSet.join());
            }
            else {
                //$(":checkbox").parent().parent().children().find(".checkbox").prop('checked', false)
                $(":checkbox").prop('checked', false)
                $(":checkbox").each(function(e,v){

                    var index = checkedRowsSet.indexOf(v.id);
                    if (index>-1) checkedRowsSet.splice(index, 1);
                })
                $("#submitAddExtractSampleIds").val(checkedRowsSet.join());
            }
        });

    }
    all=1;
}
