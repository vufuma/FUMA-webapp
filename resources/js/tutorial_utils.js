export function tutorialDownloadVariant(variant_code){
	$('#tutorialDownloadVariantCode').val(variant_code);
	$('#tutorialDownloadVariantSubmit').trigger('click');
}

export default tutorialDownloadVariant;