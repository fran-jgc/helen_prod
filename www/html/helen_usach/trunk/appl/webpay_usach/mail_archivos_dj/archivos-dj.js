$(document).ready(function () {
	cardinalidad_dj = $("#cardinalidad_dj").val();
	cardinalidad_f22 = $("#cardinalidad_f22").val();
	cardinalidad_ar = $("#cardinalidad_ar").val();
	cardinalidad_ccp = $("#cardinalidad_ccp").val();
	cardinalidad_cr = $("#cardinalidad_cr").val();
	cardinalidad_cn = $("#cardinalidad_cn").val();
	cardinalidad_cm = $("#cardinalidad_cm").val();
	cardinalidad_otros = $("#cardinalidad_otros").val();
	cardinalidad_lq = $("#cardinalidad_lq").val();
	
	max_peso = $("#max_peso").val();
	
	tp_cod_dj = $("#tp_cod_dj").val();
	tp_f22 = $("#tp_f22").val();
	tp_ar = $("#tp_ar").val();
	tp_ccp = $("#tp_ccp").val();
	tp_cr = $("#tp_cr").val();
	tp_cn = $("#tp_cn").val();
	tp_cm = $("#tp_cm").val();
	tp_otros = $("#tp_otros").val();
	tp_lq = $("#tp_lq").val();
	
	cod_dj = $("#cod_dj").val();
	cod_pers = $("#cod_pers").val();
	
	 $("#dj").dropzone({
		url: "upload.php?tp="+tp_cod_dj+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_dj,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_dj,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_dj,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				$("#liDj").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liDj").removeClass('green');
			});
		}
	 });
	
	$("#f22").dropzone({
		url: "upload.php?tp="+tp_f22+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_f22,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_f22,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_f22,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				$("#liF22").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liF22").removeClass('green');
			});
		}
	});
	
	$("#ar").dropzone({
		url: "upload.php?tp="+tp_ar+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_ar,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_ar,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_ar,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				$("#liAr").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liAr").removeClass('green');
			});
		}
	});
	
	$("#ccp").dropzone({
		url: "upload.php?tp="+tp_ccp+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_ccp,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_ccp,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_ccp,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				$("#liCcp").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liCcp").removeClass('green');
			});
		}
	});
	
	$("#cr").dropzone({
		url: "upload.php?tp="+tp_cr+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_cr,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_cr,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_cr,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				$("#liCr").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liCr").removeClass('green');
			});
		}
	});
	
	$("#cn").dropzone({
		url: "upload.php?tp="+tp_cn+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_cn,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_cn,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_cn,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				$("#liCn").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liCn").removeClass('green');
			});
		}
	});
	
	$("#cm").dropzone({
		url: "upload.php?tp="+tp_cm+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_cm,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_cm,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_cm,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				$("#liCm").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liCm").removeClass('green');
			});
		}
	});
	
	$("#otros").dropzone({
		url: "upload.php?tp="+tp_otros+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_otros,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_otros,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_otros,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				$("#liOt").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liOt").removeClass('green');
			});
		}
	});
	
	$("#lq").dropzone({
		url: "upload.php?tp="+tp_lq+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_lq,
		acceptedFiles: 'image/*,application/pdf',
		addRemoveLinks: true,
		maxFiles: cardinalidad_lq,
		autoProcessQueue: false,
		parallelUploads: cardinalidad_lq,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M�ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				$("#liLq").addClass('green');
			});
			this.on("removedfile", function (file) {
				$("#liLq").removeClass('green');
			});
		}
	});
	
	$('#acepta_condiciones_info').click(function(){ 
		$("#modal-titulo").html('Condiciones');
		$(".modal-body").html('Usted afirma que la informaci&oacute;n entregada es fidedigna.');
		$('#myModal').modal('show');
	});
	
	$('#dj_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_dj").val());
		$('#myModal').modal('show');
	});
	
	$('#f22_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_f22").val());
		$('#myModal').modal('show');
	});
	
	$('#ar_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_ar").val());
		$('#myModal').modal('show');
	});
	
	$('#cc_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_ccp").val());
		$('#myModal').modal('show');
	});
	
	$('#cr_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_cr").val());
		$('#myModal').modal('show');
	});
	
	$('#cn_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_cn").val());
		$('#myModal').modal('show');
	});
	
	$('#cm_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_cm").val());
		$('#myModal').modal('show');
	});
	
	$('#ot_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_ot").val());
		$('#myModal').modal('show');
	});
	
	$('#lq_info').click(function(){ 
		$("#modal-titulo").html('');
		$(".modal-body").html($("#ayuda_lq").val());
		$('#myModal').modal('show');
	});
	
	$('#env').click(function(){  
		var djDZ = Dropzone.forElement("#dj");
		var f22DZ = Dropzone.forElement("#f22");
		var arDZ = Dropzone.forElement("#ar");
		var ccpDZ = Dropzone.forElement("#ccp");
		var crDZ = Dropzone.forElement("#cr");
		var cnDZ = Dropzone.forElement("#cn");
		var cmDZ = Dropzone.forElement("#cm");
		var otrosDZ = Dropzone.forElement("#otros");
		var lqDZ = Dropzone.forElement("#lq");
		
		var djDZCargados = djDZ.getAcceptedFiles().length;
		var f22DZCargados = f22DZ.getAcceptedFiles().length;
		var arDZCargados = arDZ.getAcceptedFiles().length;
		var ccpDZCargados = ccpDZ.getAcceptedFiles().length;
		var crDZCargados = crDZ.getAcceptedFiles().length;
		var cnDZCargados = cnDZ.getAcceptedFiles().length;
		var cmDZCargados = cmDZ.getAcceptedFiles().length;
		var otDZCargados = otrosDZ.getAcceptedFiles().length;
		var lqDZCargados = lqDZ.getAcceptedFiles().length;
		
		var validaciones = false;
		var displayConyuge = $("#display_matrimonio").val();
		var displayHijo = $("#display_nacimiento").val();
		
		/*
		//no deber�a validar nada de archivos obligatorios al volver a cargar
		if((displayConyuge != 'none') && (cmDZCargados == 0)){
			alert('Debe ingresar su declaraci�n en "D. Jurada" y su certificado de Matrimonio en "C. Matrimonio"');
			return false;
		}
		
		if((displayHijo != 'none') && (cnDZCargados == 0)){
			alert('Debe ingresar su declaraci�n en "D. Jurada" y el certificado de Nacimiento de su hijo en "C. Nacimiento"');
			return false;
		}
		
		if( ((djDZCargados > 0) && (f22DZCargados 	> 0)) || 
			((djDZCargados > 0) && (arDZCargados 	> 0)) || 
			((djDZCargados > 0) && (ccpDZCargados 	> 0)) ||
			((djDZCargados > 0) && (crDZCargados 	> 0)) || 
			((djDZCargados > 0) && (otDZCargados 	> 0)) ||
			((djDZCargados > 0) && (lqDZCargados 	> 0))){
			validaciones = true
		}
		*/
		
		validacion_dj = $("#VALIDA_DJ").val();
		validacion_f22 = $("#VALIDA_F22").val();
		validacion_retenedores = $("#VALIDA_RETENEDORES").val();
		validacion_cotizaciones = $("#VALIDA_COTIZACIONES").val();
		validacion_remuneraciones = $("#VALIDA_REMUNERACIONES").val();
		validacion_nacimiento = $("#VALIDA_NACIMIENTO").val();
		validacion_matrimonio = $("#VALIDA_MATRIMONIO").val();
		validacion_liquidaciones = $("#VALIDA_LIQUIDACIONES").val();
		validacion_otros = $("#VALIDA_OTROS").val();
		
		if(validacion_dj == "" && djDZCargados == 0){
			alert('Debe ingresar archivo en "D. Jurada"');
			return false;
		}
		else if(validacion_f22 == "" && f22DZCargados == 0){
			alert('Debe ingresar archivo en "Formulario 22"');
			return false;
		}
		else if(validacion_retenedores == "" && arDZCargados == 0){
			alert('Debe ingresar archivo en "Agentes retenedores"');
			return false;
		}
		else if(validacion_cotizaciones == "" && ccpDZCargados == 0){
			alert('Debe ingresar archivo en "Certificado de cotizaciones"');
			return false;
		}
		else if(validacion_remuneraciones == "" && crDZCargados == 0){
			alert('Debe ingresar archivo en "Certificado de remuneraciones"');
			return false;
		}
		else if(validacion_nacimiento == "" && cnDZCargados == 0){
			alert('Debe ingresar archivo en "Certificado de nacimiento de hijo"');
			return false;
		}
		else if(validacion_matrimonio == "" && djDZCargados == 0){
			alert('Debe ingresar archivo en "Certificado de matrimonio"');
			return false;
		}
		//else if(validacion_liquidaciones == "" && lqDZCargados == 0){
		//	alert('Debe ingresar archivo en "Finiquitos o Licencias Medicas"');
		//	return false;
		//}
		else if(validacion_otros == "" && otDZCargados == 0){
			alert('Debe ingresar archivo en "Otros"');
			return false;
		}
		
		
		if($('#acepta_condiciones').prop('checked')) {
			confirmacion = window.confirm('Al realizar esta acci�n ya no podr� modificar los archivos que esta subiendo �Esta seguro?');
			
			if(confirmacion){
				$("#DIV_UPLOADS").addClass('hidden');
				$("#DIV_LOADER").removeClass('hidden');
				var promises = [
					subirDZ(djDZ,'dj'),
					subirDZ(f22DZ,'f22'),
					subirDZ(arDZ,'ar'),
					subirDZ(ccpDZ,'cc'),
					subirDZ(crDZ,'cr'),
					subirDZ(cnDZ,'cn'),
					subirDZ(cmDZ,'cm'),
					subirDZ(otrosDZ,'ot'),
					subirDZ(lqDZ,'lq'),
				];
				
				Promise.all(promises).then(values => {
					//enviar_mail_confirmado()
					$("#DIV_LOADER").addClass('hidden');
					$("#DIV_EXITO").removeClass('hidden');
					console.log('TODOS EJECUTADOS');
				});
			}// fin confirmacion
		}else{
			alert('Debe aceptar las condiciones');
			$('#acepta_condiciones').focus();
			return false;
		}
		
		
	});
});

subidos = '';

function subirDZ (dz,nombreDz) {
	const promise = new Promise(function (resolve, reject) {
		var ar_subidos = dz.getAcceptedFiles().length;
		if(ar_subidos > 0){
			
			dz.processQueue();
			
			dz.on("success", function(file,response) {
				//alert('subido'+ nombreDz);
				resolve(subidos= subidos+'|'+nombreDz);
			});
		
			dz.on("error", function (file, error, xhr) {
	          //  var ficheiro = { nome: file.name, status: xhr.status, statusText: xhr.statusText, erro: error.message };
	          //  $scope.$apply($scope.errorFiles.push(ficheiro));
				reject(console.log('No se pudo subir'));
        	});
		}else
			resolve(console.log('Sin archivos para subir en '+nombreDz));
		
	})
	return promise

}

function enviar_mail_confirmado(){
	cod_dj = $("#cod_dj").val();
	cod_pers = $("#cod_pers").val();
	
	var result;
	parametros = {                   
			"cod_dj" : cod_dj,
			"cod_pers" : cod_pers
		};
	url = "ajax_confirmacion_dj.php";
	$.ajax({    
		async: false,
		url: url,
		data: parametros, 
        
		success: function(data)             
		{	
			$("#DIV_LOADER").addClass('hidden');
			$("#DIV_EXITO").removeClass('hidden');
			
		}
	});
}


