(()=>{"use strict";function e(e){return decodeURIComponent(atob(e).split("").map((function(e){return"%"+("00"+e.charCodeAt(0).toString(16)).slice(-2)})).join(""))}var o,t,i,d,n,l,a,c,r,s;o=jQuery,t=window.ModuleBuilderModal,i=igd.settings,d=i.wooCommerceDownload,n=void 0===d||d,l=i.wooCommerceUpload,a=void 0!==l&&l,c=i.dokanUpload,r=void 0!==c&&c,s={init:function(){n&&(s.addSelectFilesButton(),o("#variable_product_options").on("woocommerce_variations_added",s.addSelectFilesButton),o("#woocommerce-product-data").on("woocommerce_variations_loaded",s.addSelectFilesButton),o(document).on("click",".igd-wc-button",s.selectFiles)),(a||r)&&(o("#igd-wc-select-parent-folder").on("click",s.selectParentFolder),o(document).on("click",".upload-folder-name-field .variable",s.handlePlaceholder),o("input#_uploadable").on("change",s.handleUploadable),o("input#_uploadable").trigger("change"),o("input#_igd_upload").on("change",s.handleGoogleDriveSettings),o("input#_igd_upload").trigger("change"))},handleGoogleDriveSettings:function(){var e=o("input#_igd_upload:checked").length;o(".show_if_igd_upload").hide(),o(".hide_if_igd_upload").hide(),e&&(o(".hide_if_igd_upload").hide(),o(".show_if_igd_upload").show())},handleUploadable:function(){var e=o(this).is(":checked");o(".show_if_uploadable").hide(),o(".hide_if_uploadable").hide(),e&&(o(".hide_if_uploadable").hide(),o(".show_if_uploadable").show())},handlePlaceholder:function(){var e=o(this).text(),t=o(this).closest(".upload-folder-name-field").find("input");t.val(t.val()+" "+e)},addSelectFilesButton:function(){o(".downloadable_files").each((function(){o(this).find("tfoot th:last-child").append('<div class="igd-woocommerce"><button type="button" class="button button-secondary igd-wc-button"><img src="'.concat(igd.pluginUrl,'/assets/images/drive.png" width="20" /><span>').concat(wp.i18n.__("Add File","integrate-google-drive"),"</span></button></div>"))}))},selectFiles:function(){var e=o(this).parents(".downloadable_files");Swal.fire({html:'<div id="igd-select-files" class="igd-module-builder-modal-wrap"></div>',showConfirmButton:!1,customClass:{container:"igd-swal igd-module-builder-modal-container"},didOpen:function(i){ReactDOM.render(React.createElement(t,{initData:{},onUpdate:function(t){var i=t.folders,d=void 0===i?[]:i,n=t.woocommerceRedirect,l=t.woocommerceAddPermission,a=e.find("tbody"),c=e.find(".button.insert").data("row");d.map((function(e){var t=e.id,i=e.name,d=e.type,r=e.accountId,s="https://drive.google.com/open?action=igd-wc-download&id=".concat(t,"&account_id=").concat(r,"&type=").concat(d,"&redirect=").concat(n||"","&create_permission=").concat(l||""),u=o(c);u.find(".file_name .input_text").val(i),u.find(".file_url .input_text").val(s),a.append(u)})),Swal.close()},onClose:function(){return Swal.close()},isWooCommerce:!0}),document.getElementById("igd-select-files"))},willClose:function(e){ReactDOM.unmountComponentAtNode(document.getElementById("igd-select-files"))}})},selectParentFolder:function(){Swal.fire({html:'<div id="igd-select-files" class="igd-module-builder-modal-wrap"></div>',showConfirmButton:!1,customClass:{container:"igd-swal igd-module-builder-modal-container"},didOpen:function(i){var d,n=document.getElementById("igd-select-files"),l=null===(d=o("#igd_upload_parent_folder").val())||void 0===d?void 0:d.trim(),a=l&&""!==l&&'""'!==l?[JSON.parse(l)]:[];ReactDOM.render(React.createElement(t,{initData:{folders:a},onUpdate:function(t){var i,d=t.folders,n=(void 0===d?[]:d)[0];o("#igd_upload_parent_folder").val(JSON.stringify(n)),o(".parent-folder-account").text(JSON.parse(e(igd.accounts))[n.accountId]?null===(i=JSON.parse(e(igd.accounts))[n.accountId])||void 0===i?void 0:i.email:""),o(".parent-folder-name").text(n.name),Swal.close()},onClose:function(){return Swal.close()},isSelectFiles:!0,selectionType:"parent"}),n)},willClose:function(e){var o=document.getElementById("igd-select-files");ReactDOM.unmountComponentAtNode(o)}})}},o(document).ready(s.init)})();