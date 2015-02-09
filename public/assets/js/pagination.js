/**
 * Created by luis on 9/2/14.
 */

//configure REST URLs

var url=_.last(document.URL.split("/"));
var url_ =_.last(document.URL.split("/"));
var searchUrl='search';

$(document).ready(function(){
    var pages;

    $("#tableLength").on("change", function(){getRepaginationData();})
    $("#faAvancedSearch").on("click", function(){quickSearch();})
    $("#checkbox").on("click", function(){activateTableSelections();})

    if (navigator.userAgent.search("Firefox") >= 0) {
        console.log(1);
        $("#dataTables_filter").html('<div id="dataTables_filter" class="dataTables_filter"> <label>Quick Search:<div class="input-group"><input type="search" role="search" id="quickSearch" class="form-control input-sm" placeholder="" aria-controls="isolatesTable" ><span class="input-group-addon" onclick="$(&#39;#quickSearch&#39;).val(&#39;&#39;);eraseSearch()"> x</span></div><i class="fa fa-search fa-3" id="faAvancedSearch" onclick="quickSearch()" style="margin-left:5px"></i></label></div>');
    };

    $('#quickSearch').on('keypress', function(e) {
        if(e.keyCode==13){
            quickSearch();
        }
    });

    $('td').delegate('#editField', 'keypress', function(e) {
        if(e.keyCode==13){
            act(this);
        }
    });

    //Reset the search when click "clear" button inside search box
    $('#quickSearch').on("click", function(){
        setTimeout(function(){eraseSearch()}, 100);
    })

    function eraseSearch(){
        if( $('#quickSearch').val().length<1){
            $("#faAvancedSearch").click();
        }
    }

    $('th.sorting').on("click", function(){
        sortByColumn(this);
    });

    $("tbody").delegate("input.checkbox", "change", function(){checkedRows(this)});

    //clear the checkboxes if the user pushes the backbutton
    $(":checkbox").prop('checked', false);

    setPaginationNavigation();
}); //doc.ready

var checkedRowsSet=new Array();
function checkedRows(elem){
    self=elem;
    var index = checkedRowsSet.indexOf(self.id);
    if(self.checked) {
        if (index<0) {
            checkedRowsSet.push(self.id)

        }
    }
    else {
        if (index>-1) checkedRowsSet.splice(index, 1);
        if($("#all").is(":checked")) $("#all").prop("checked", false);
    }
    var values_ = checkedRowsSet.join();
    $("#submitAddExtractSampleIds").val(values_);
    $("#submitExportSampleIds").val(values_);
    //$("#submitGetExtractSampleIds").val(values_);
}


//checkboxes
var all=0;
function activateTableSelections(){
    var button1=$("#submitAddExtractSampleIds");
    var button2=$("#submitExportSampleIds");
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
            var values_ = checkedRowsSet.join();
            button1.val(values_);
            button2.val(values_);
            //$("#submitGetExtractSampleIds").val(values_);
            // console.log($("#submitAddExtractSampleIds").val())
            // console.log(checkedRowsSet.join())
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
                button1.val(checkedRowsSet.join());
                button2.val(checkedRowsSet.join());
                //$("#submitGetExtractSampleIds").val(checkedRowsSet.join());
            }
            else {
                //$(":checkbox").parent().parent().children().find(".checkbox").prop('checked', false)
                $(":checkbox").prop('checked', false)
                $(":checkbox").each(function(e,v){
                    var index = checkedRowsSet.indexOf(v.id);
                    if (index>-1) checkedRowsSet.splice(index, 1);
                })
                button1.val(checkedRowsSet.join());
                button2.val(checkedRowsSet.join());
                //$("#submitGetExtractSampleIds").val(checkedRowsSet.join());
            }
        });
    }
    all=1;
}

function setCheckboxes(){
    console.log("setCheckboxes()");
    $(":checkbox").each(function(e,v){
        var index =checkedRowsSet.indexOf(v.id);
        if (index>-1) {
            console.log(v.id);
            var _id= "#"+v.id + ".checkbox";
            console.log(_id);
            $(_id).prop("checked", "true")
        }
    })
    var a=1;
    $(":checkbox").each(function(e,v){
        //console.log(e,v);
        if(v.id!="all"){
            if(!$(v).is(":checked")) {
                a=0;
            }}})

    if (a==1){
        $("#all").prop('checked', true);
    }
    else {
        $("#all").prop('checked', false);
    }
}

function preparePagination(count, pageSize){
    var total=Math.ceil(count/pageSize);
    pages=total;
    //$("#isolatesTable_wrapper").after('<div class="col-xs-12 paginationWrapper" style="padding-left:15px"><ul class="pagination" id="pagination" style="margin:5px; float:left; font-size:12px"><li class="paginate_button previous disabled" aria-controls="isolatesTable" tabindex="-4" id="isolatesTable_previous"><a href="#">Previous</a></li></div>');
    $("#pagination.pagination").html('<li class="paginate_button previous disabled" aria-controls="isolatesTable" tabindex="-4" id="isolatesTable_previous"><a href="#">Previous</a></li>');
    if(total>7){
        var total_=5;
        $("#isolatesTable_previous").after('<li class="paginate_button active" aria-controls="isolatesTable" tabindex="1" onClick="getData(this);return 0;"><a href="#">1</a></li>');
        var el="";
        for (i=2; i<total_+1; i++){
            var el=el+'<li class="paginate_button " aria-controls="isolatesTable" tabindex="' + i +'" onClick="getData(this);return 0;"><a href="#">' + i +'</a></li>';
        }
        ($("ul.pagination").children().last()).after(el);
        ($("ul.pagination").children().last()).after('<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-2" id="isolatesTable_ellipsis"><a href="#">...</a></li><li class="paginate_button " aria-controls="isolatesTable" tabindex="'+total+'" onClick="getData(this);return 0;"><a href="#">'+total+'</a></li>');
    }
    else {
        if(total>0){
            var el="";
            for(var i=1; i<total+1; i++){
                var el= el+'<li class="paginate_button " aria-controls="isolatesTable" tabindex="'+i+'" onClick="getData(this);return 0;"><a href="#">'+i+'</a></li>';
            }
            ($("ul.pagination").children().last()).after(el);
        }
    }
    ($("ul.pagination").children().last()).after('<li class="paginate_button next disabled" aria-controls="isolatesTable" tabindex="-1" id="isolatesTable_next"><a href="#">Next</a></li>');

    setButtons(total, count);

    var el='<div class="col-xs-12 showingPagesInfo" style="margin-top:10px; padding-left:20px">';
    el=el+'<div class="dataTables_info" id="tableInfo" role="status" aria-live="polite" style="font-size:12px">';
    el=el+'Showing 1 to 10 of '+count+' entries (filtered from '+count+' total entries)</div></div>'
    $(".paginationWrapper").after(el);
}

function setPaginationNavigation(){
    $.ajax({
        type: 'get',
        url: url,
        data: {length:10, offset:0, column:"idIsolates", order:"sorting_desc".split("_")[1], "ajax":1},
        //data: {id:"1"},
        beforeSend: function(){
        },
        success: function (response) {
            //console.log("a: " + response);
            var response=JSON.parse(response);
            preparePagination(response.count,response.size);
        },
        error: function() {
            console.error("error");
        }
    });
}

function setButtons(pages, records){
    var totalRecords=records;
    console.log("records: " + records);
    if (totalRecords>7){
        $('.next').removeClass("disabled");
    }
    if(pages==1){
        $('.previous').addClass('disabled');
        $('.next').addClass('disabled');
    }
    var elipsis0_='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
    var elipsis1_='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-2" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
    var elipsis1 = $($.parseHTML(elipsis1_));
    var elipsis0 = $($.parseHTML(elipsis0_));
    var first   = '<li class="paginate_button " aria-controls="isolatesTable" tabindex="1" onclick="getData(this);return 0;"><a href="#">1</a></li>';
    var first_  =  $($.parseHTML(first));
    var second_ = '<li class="paginate_button " aria-controls="isolatesTable" tabindex="2" onclick="getData(this);return 0;"><a href="#">2</a></li>';
    var second  =  $($.parseHTML(second_));
    //'NEXT' BUTTON
    $('.next').on('click', function(){
        if ( $('.next').attr("class").indexOf("disabled")>-1){
            // console.log("done");
            return 0; //if end of list, don't respond to click
        }
        if ( $('.previous').attr("class").indexOf("disabled")>-1){
            $('.previous').removeClass('disabled');
        }
        var active=$(".active");

        if ($(active).attr('tabindex')==pages-1) {
            $('.next').addClass("disabled"); //if at end of list, disable this "next" button
        }
        var a;
        var elipsis0;
        var next;
        if (pages>2){ //must handle case of pages=2 differently. see bellow
            if ($(active).attr('tabindex')<5 || $($('*.pagination').children().get(3)).attr('tabindex')>=pages-4) {
                //console.log("5_1");
                if ($($('*.pagination').children().get(3)).attr('tabindex')>=pages-4) {
                    // console.log("pages-4");
                    var secondToLast=$(first_).attr('tabindex', pages-1 );
                    $(secondToLast).children().html(pages-1);
                    a = $('*.pagination').children().get(-3);
                    $(a).replaceWith(secondToLast);
                }
                next = $(active).next();
                $(active).removeClass("active");
                $(next).addClass("active");
                getData($(next),0);
            }
            else if ($(active).attr('tabindex')>=5 && $($('*.pagination').children().get(3)).attr('tabindex')<pages-4 ) {
                //console.log("5_2");
                elipsis0 = $('#isolatesTable_ellipsis').clone(true);
                $(second).replaceWith(elipsis0);

                var firstElipsis = $('*.pagination').children().get(2);
                elipsis0='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
                var $elipsis0 = $($.parseHTML(elipsis0));

                $(firstElipsis).replaceWith($elipsis0.clone());

                a=$('.paginate_button');

                for (var i=3; i<a.length-3; i++){
                    $(a[i]).attr("tabindex", parseInt($(a[i]).attr("tabindex")) +1);
                    $(a[i]).children().html(parseInt($(a[i]).children().html())+1);
                }
                getData($(active),0);
            }
        }
        else if(pages==2){
            next = $(active).next();
            $(active).removeClass("active");
            $(next).addClass("active");
            getData($(next),0);
        }
    });

    //'PREVIOUS' BUTTON
    $('.previous').on('click', function(){
        var active;
        if ( $('.previous').attr("class").indexOf("disabled")>-1){
            // console.log("done");
            return 0;
        }
        if ( $('.next').attr("class").indexOf("disabled")>-1){
            $('.next').removeClass('disabled');
        }
        active=$(".active");
        if ($($('*.pagination').children().get(3)).attr('tabindex')<=3 || $(active).attr('tabindex')>pages-4) {
            //  console.log("5_1");

            if ($($('*.pagination').children().get(5)).attr('tabindex')<=5) {

                if ($($('*.pagination').children().get(2)).attr("tabindex")=="-3"){
                    //console.log("replace elipsis");
                    var first_=$($('*.pagination').children().get(1)).clone();
                    $(first_).attr("tabindex", 2);
                    $(first_).children().html(2);
                    var second = $('*.pagination').children().get(2);
                    //var elipsis0='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>'
                    $(second).replaceWith(first_);
                }
            }
            active=$(".active");
            var previous = $(active).prev();
            $(active).removeClass("active");
            $(previous).addClass("active");
            active=$(".active");
            getData($(active),0);
            if ($(active).attr('tabindex')==1) {
                $('.previous').addClass("disabled");
            }
        }

        else if ($($('*.pagination').children().get(3)).attr('tabindex')>3  && $($('*.pagination').children().get(3)).attr('tabindex')<=pages-4 ) {
            // console.log("5_2");
            var elipsis1='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-2" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
            var $elipsis1 = $($.parseHTML(elipsis1));
            var secondElipsis = $('*.pagination').children().get(6);
            $(secondElipsis).replaceWith($elipsis1.clone());
            var a=$('.paginate_button');
            for (var i=3; i<a.length-3; i++){
                $(a[i]).attr("tabindex", parseInt($(a[i]).attr("tabindex")) -1);
                $(a[i]).children().html(parseInt($(a[i]).children().html())-1);
            }
            getData($(active),0);
        }
    });
    //when number of pages is low, the template doesnt to set the page 1 button separate from the others, it
    //just iterates through, and so it's class doesn not get set to "active"
    //in the future, remove setting the initial pagination buttons from the template, and into javascript
    $($('*.pagination').children().get(1)).addClass('active');
} //setbuttons


//redo the pagination menu when changing the selection for the number of rows to display
function getRepaginationData(elem){
    var tableLength = $("#tableLength").children().children().val();
    var offset=0;
    var column;
    var order;
    $('#quickSearch').val("");
    if (elem){
        column=$(elem).attr("name");
        order=$(elem).attr("class").split(" ")[1];

        //$($("th")[0]).attr("class","");
        //$($("th")[12]).attr("class","");
    }
    else{
        if($(".sort").length > 0){
            getRepaginationData($(".sort"));
            return 0;
        }
        else {
            column = "id";
            order  = "sorting_asc";
        }
    }

    $.ajax({
        type: 'get',
        url: url,
        data: {length:tableLength, offset:offset,column: column, order:order.split("_")[1], ajax:1},
        //data: {id:"1"},
        beforeSend: function(){
        },
        success: function (response) {
            var data=JSON.parse(response);
            console.log("repaginating");
            $(".showingPagesInfo").remove();
            preparePagination(data.count, data.size);
            tbodyTemplate(data);
        },
        error: function() {
            console.error("error");
            // alert('Not working!');
        }
    });
}

function sortByColumn(elem){
    var order;
    //if ($(elem).html()=="Select" || $(elem).html()=="Details") return 0;
    var sortClass=$(elem).attr("class");
    if (sortClass=="sorting" || sortClass == "sort sorting_desc") {
        order="sorting_asc";
        $("th").attr("class", "sorting");
        $(elem).attr("class", "sort sorting_asc");
    }
    else {
        order="sorting_desc";
        $("th").attr("class", "sorting");
        $(elem).attr("class", "sort sorting_desc");
    }
    var searchTerm = $("#quickSearch").val();
    if (searchTerm===""){
        getRepaginationData(elem);
    }
    else {
        //  console.log("sbc_qs")
        quickSearch();
    }
}

function quickSearch(offset,source){
    var column;
    var order;
    // console.log("qs");
    //$("th").attr("class", "sorting");

    if ($(".sort").length>0){
        var elem=$(".sort");
        column=$(elem).attr("name");
        order=$(elem).attr("class").split(" ")[1];
    }
    else {
        column="id";
        order="sorting_asc";
    }

    var searchTerm = $("#quickSearch").val();
    if (searchTerm===""){getRepaginationData(); return 0;}
    // console.log("bypassed repagination", column, order);
    var tableLength = $("#tableLength").children().children().val();
    //var offset=tableLength * parseInt($(elem).attr('tabindex'))-tableLength;
    if (!offset){
        offset=0;
    }
    else {
        offset=offset;
    }
    // console.log("offset: " + offset);
    $.ajax({
        type: 'post',
        url: "search",
        data: {table: _.last(document.URL.split("/")), length:tableLength, offset:offset, search:searchTerm, column: column, order:order.split("_")[1]},
        //data: {id:"1"},
        beforeSend: function(){
            //console.log({length:tableLength, offset:offset, search:searchTerm, column: column, order:order});
        },
        success: function (response) {
            //console.log(response);
            var data=JSON.parse(response);
            if (!source) {
                $(".showingPagesInfo").remove()
                preparePagination(data.count, data.size);
                console.log("a :" + data.count);
            }
            tbodyTemplate(data);
            setPageInfo(data.size, data.offset, data.count, data.total);
        },
        error: function() {
            console.error("error");
        }
    });
}

function navigatePagination(elem, tag){
    //console.log("getData() tabindex: " + $(elem).attr("tabindex"));
    var elipsis0='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-3" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
    var elipsis1='<li class="paginate_button disabled" aria-controls="isolatesTable" tabindex="-2" id="isolatesTable_ellipsis"><a href="#">...</a></li>';
    //var el='<li class="paginate_button" aria-controls="isolatesTable" tabindex="3" onclick="getData(this);return 0;"><a href="#"></a></li>';
    var ind=$(elem).attr("tabindex");
    if (pages>1){
        if(ind==pages){
            // console.log("00");
            $(".next").addClass("disabled");
            $(".previous").removeClass("disabled");
        }
        else if(ind==1){
            // console.log("11");
            $(".previous").addClass("disabled");
            $(".next").removeClass("disabled");
        }
        else {
            // console.log("12");
            $(".previous").removeClass("disabled");
            $(".next").removeClass("disabled");
        }
    }
    if (ind>5 && ind < pages-5){
        $($('*.pagination').children().get(2)).replaceWith(elipsis0);
        $($('*.pagination').children().get(6)).replaceWith(elipsis1);
    }
    if (tag!==0){ //distinguish between calling from the pagination numbers, or from the pagination next/previous buttons
        if($(elem).attr("tabindex")==pages && pages>7){
            $(".next").addClass("disabled");
            el=$($('*.pagination').children().get(4)).clone(true);
            $($('*.pagination').children().get(6)).replaceWith(el);
            $($('*.pagination').children().get(6)).attr("tabindex", pages-1);
            $($('*.pagination').children().get(6)).children().html(pages-1);
            $($('*.pagination').children().get(5)).attr("tabindex", pages-2);
            $($('*.pagination').children().get(5)).children().html(pages-2);
            $($('*.pagination').children().get(4)).attr("tabindex", pages-3);
            $($('*.pagination').children().get(4)).children().html(pages-3);
            $($('*.pagination').children().get(3)).attr("tabindex", pages-4);
            $($('*.pagination').children().get(3)).children().html(pages-4);
            var second = $('*.pagination').children().get(2);
            $(second).replaceWith(elipsis1);
            $(second).attr("tabindex", -2);
            if ($('.previous').attr("class").indexOf("disabled")>-1)$(".previous").removeClass("disabled");
        }
        else if($(elem).attr("tabindex")==1 && pages >7){
            $(".previous").addClass("disabled");
            var el=$($('*.pagination').children().get(3)).clone(true);
            $($('*.pagination').children().get(2)).replaceWith(el);
            $($('*.pagination').children().get(2)).attr("tabindex", "2");
            $($('*.pagination').children().get(2)).children().html("2");
            $($('*.pagination').children().get(3)).attr("tabindex", "3");
            $($('*.pagination').children().get(3)).children().html("3");
            $($('*.pagination').children().get(4)).attr("tabindex", "4");
            $($('*.pagination').children().get(4)).children().html("4");
            $($('*.pagination').children().get(5)).attr("tabindex", "5");
            $($('*.pagination').children().get(5)).children().html("5");
            var secondToLast = $('*.pagination').children().get(-3);
            $(secondToLast).replaceWith(elipsis1);
            if ($('.next').attr("class").indexOf("disabled")>-1)$(".next").removeClass("disabled");
        }
        if($(elem).attr("tabindex")>1 && $(elem).attr("tabindex")< pages) {
            if ($('.next').attr("class").indexOf("disabled")>-1)$(".next").removeClass("disabled");
            if ($('.previous').attr("class").indexOf("disabled")>-1)$(".previous").removeClass("disabled");
            if($(elem).attr("tabindex")>5 && ($(elem).attr("tabindex")!=pages)){
                //console.log("9");
                if($($('*.pagination').children().get(-2)).attr('tabindex')!=-3){
                    //var elipsis0 = $('#isolatesTable_ellipsis').clone(true);
                    $($('*.pagination').children().get(2)).replaceWith($($.parseHTML(elipsis0)).clone());
                }
            }
        }
    }

    $('*.paginate_button').removeClass('active');
    $(elem).addClass('active');
    var tableLength = $("#tableLength").children().children().val();
    offset=tableLength * parseInt($(elem).attr('tabindex'))-tableLength;
    //console.log(offset);
}

function setPageInfo(size, offset, count, total){
    var lowerlimit=parseInt(offset)+1;
    var upperlimit=parseInt(size)+ parseInt(offset);
    (upperlimit > count)?upperlimit = count:upperlimit;
    var info='Showing ' + lowerlimit + ' to ' + upperlimit +' of '+count+' entries (filtered from '+ total + ' total entries)';
    $("#tableInfo").html(info);
}

function tbodyTemplate(data){
    var tbody=$('table').find('tbody')[0];
    $(tbody).empty();
    for(var x in data.data){
        var row = data.data[x];
        var tr= '<tr id="'+row.id+'"></tr>';
        $(tbody).append(tr);
        var raw_template = $("script[type='text/x-handlebars-template']").html();
        var template = Handlebars.compile(raw_template);
        var placeHolder = $(tbody).find('tr').last();
        var html = template(row);
        placeHolder.append(html);
    }
}

function getData(elem, tag) {
    var tableLength = $("#tableLength").children().children().val();
    var offset = tableLength * parseInt($(elem).attr('tabindex'))-tableLength;
    var column;
    var order;

    if(elem){ //if called from pagination, and not from the number of rows selection menu
        navigatePagination(elem, tag);
    }
    else { //if called from the #rows selection menu, instead of from the pagination menu
        offset=0;
    }
    if ($("#quickSearch").val()!==""){
        quickSearch(offset, 1);
    }
    else {
        if($(".sort").length > 0){
            elem=$(".sort");
            column=$(elem).attr("name");
            console.log(column);
            order=$(elem).attr("class").split(" ")[1];
        }
        else {
            column = "id";
            order  = "sorting_asc";
        }

        $.ajax({
            type: 'get',
            url: url,
            data: {length:tableLength, offset:offset, column:column, order:order.split("_")[1], ajax:1},
            beforeSend: function(){
            },
            success: function (response) {
                console.log("gotdata");
                var data=JSON.parse(response);
                console.log(data.offset);
                pages=Math.ceil(data.count/data.size);
                if ($($('*.pagination').children().get(-2)).attr('tabindex')!=pages && pages>0){
                    $($('*.pagination').children().get(-2)).attr('tabindex', pages);
                    $($('*.pagination').children().get(-2)).children().html(pages);
                }

                tbodyTemplate(data);
                setPageInfo(data.size, data.offset, data.count, data.total);
                //setCheckboxes(); //function is in tableAjax.js
            },
            error: function() {
                console.error("error");
            }
        });
    }
} //getData()

function test(){
    return 1;
}