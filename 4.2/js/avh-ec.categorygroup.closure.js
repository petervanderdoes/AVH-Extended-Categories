jQuery(document).ready(function(a){a("#the-list").wpList({delBefore:function(b){if("undefined"!=showNotice)return showNotice.warn()?b:false;return b}});a('.delete a[class^="delete"]').live("click",function(){return false})});