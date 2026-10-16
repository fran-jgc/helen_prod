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
	accepted_files = 'image/*,application/pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document';
	var modo_debug = $("#modo_debug").val();
	
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
		url: "upload.php?tp="+tp_cod_dj+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_dj+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_dj,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_cod_dj, file);
				$("#liDj").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_cod_dj,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_cod_dj, file);
				removeListJson(tp_cod_dj,fileName);
				
				$("#liDj").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_cod_dj,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_cod_dj,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	 });
	
	$("#f22").dropzone({
		url: "upload.php?tp="+tp_f22+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_f22+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_f22,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_f22, file);
				$("#liF22").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_f22,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_f22, file);
				removeListJson(tp_f22,fileName);

				if(this.getAcceptedFiles().length == 0)
					$("#liF22").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_f22,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_f22,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	});
	
	$("#ar").dropzone({
		url: "upload.php?tp="+tp_ar+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_ar+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_ar,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_ar, file);
				$("#liAr").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_ar,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_ar, file);
				removeListJson(tp_ar,fileName);
				
				if(this.getAcceptedFiles().length == 0)
					$("#liAr").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_ar,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_ar,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	});
	
	$("#ccp").dropzone({
	
		url: "upload.php?tp="+tp_ccp+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_ccp+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_ccp,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_ccp, file);
				$("#liCcp").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_ccp,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_ccp, file);
				removeListJson(tp_ccp,fileName);

				if(this.getAcceptedFiles().length == 0)
					$("#liCcp").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_ccp,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_ccp,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	});
	
	$("#cr").dropzone({
		
		url: "upload.php?tp="+tp_cr+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_cr+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_cr,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_cr, file);
				$("#liCr").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_cr,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_cr, file);
				removeListJson(tp_cr,fileName);

				if(this.getAcceptedFiles().length == 0)
					$("#liCr").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_cr,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_cr,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	});
	
	$("#cn").dropzone({
		url: "upload.php?tp="+tp_cn+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_cn+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_cn,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_cn, file);
				$("#liCn").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_cn,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_cn, file);
				removeListJson(tp_cn,fileName);

				if(this.getAcceptedFiles().length == 0)
					$("#liCn").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_cn,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_cn,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	});
	
	$("#cm").dropzone({
		url: "upload.php?tp="+tp_cm+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_cm+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_cm,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_cm, file);
				$("#liCm").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_cm,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_cm, file);
				removeListJson(tp_cm,fileName);

				if(this.getAcceptedFiles().length == 0)
					$("#liCm").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_cm,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_cm,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
			
		}
	});
	
	$("#otros").dropzone({
		url: "upload.php?tp="+tp_otros+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_otros+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_otros,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_otros, file);
				$("#liOt").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_otros,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_otros, file);
				removeListJson(tp_otros,fileName);

				if(this.getAcceptedFiles().length == 0)
					$("#liOt").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_otros,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_otros,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	});
	
	$("#lq").dropzone({
		url: "upload.php?tp="+tp_lq+"&cod_dj="+cod_dj+"&cardinalidad="+cardinalidad_lq+"&debug="+modo_debug+"&cod_pers="+cod_pers,
		acceptedFiles: accepted_files,
		addRemoveLinks: true,
		maxFiles: cardinalidad_lq,
		autoProcessQueue: false,
		parallelUploads: 1,
		maxFilesize:max_peso,
		init: function () {
			this.on("maxfilesexceeded", function (file) {
				var txtArchivo = 'archivo.';
				this.removeFile(file);
				if(this.options.maxFiles > 1)
					txtArchivo = 'archivos.';
		    	   
		       alert("M\u00E1ximo "+ this.options.maxFiles+" "+txtArchivo);
			});
			this.on("addedfile", function (file) {
				registrarArchivoPendiente(tp_lq, file);
				$("#liLq").addClass('green');
			});
			this.on("processing", function (file) {
				addListJson(tp_lq,file.name,file.size);
			});
			this.on("removedfile", function (file) {
				var fileName = file.name;
				quitarArchivoPendiente(tp_lq, file);
				removeListJson(tp_lq,fileName);
				
				if(this.getAcceptedFiles().length == 0)
					$("#liLq").removeClass('green');
			});
			this.on("success", function(file,response) {
           		if(modo_debug == 'S'){
					agregarAlLog(tp_lq,file);
				}
			});
			this.on("error", function (file, error, xhr) {
	            if(modo_debug == 'S'){
					agregarAlLog(tp_lq,file,error, xhr);
				}
			});
			this.on("complete", function () {
				registrarFinSubida();
			});
		}
	});
	
	$('#acepta_condiciones_info').click(function(){ 
		$("#modal-titulo").html('Condiciones');
		$(".modal-body").html('Usted afirma que la informaci\u00F3n entregada es fidedigna.');
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
		if($('#acepta_condiciones').prop('checked')) {
			confirmacion = window.confirm('Al realizar esta acci\u00F3n ya no podr\u00E1 modificar los archivos que esta subiendo \u00BFEsta seguro?');
			if(confirmacion){
				var djDZ = Dropzone.forElement("#dj");
				var f22DZ = Dropzone.forElement("#f22");
				var arDZ = Dropzone.forElement("#ar");
				var ccpDZ = Dropzone.forElement("#ccp");
				var crDZ = Dropzone.forElement("#cr");
				var cnDZ = Dropzone.forElement("#cn");
				var cmDZ = Dropzone.forElement("#cm");
				var otrosDZ = Dropzone.forElement("#otros");
				var lqDZ = Dropzone.forElement("#lq");
				
				var djDZCargados = contarArchivosPendientes(tp_cod_dj);
				var f22DZCargados = contarArchivosPendientes(tp_f22);
				var arDZCargados = contarArchivosPendientes(tp_ar);
				var ccpDZCargados = contarArchivosPendientes(tp_ccp);
				var crDZCargados = contarArchivosPendientes(tp_cr);
				var cnDZCargados = contarArchivosPendientes(tp_cn);
				var cmDZCargados = contarArchivosPendientes(tp_cm);
				var otDZCargados = contarArchivosPendientes(tp_otros);
				var lqDZCargados = contarArchivosPendientes(tp_lq);
				
				var validaciones = false;
				var revisado = $('#revisado').val();
				var displayConyuge = $("#display_conyuge").val();
				var displayHijo = $("#display_hijo").val();
				
				/*si revisado es 0 significa que es primera vez que intenta subir archivos*/
				if($('#revisado').val().length == 0){
					if((displayConyuge != 'none') && (cmDZCargados == 0)){
						alert('Debe ingresar el certificado de Matrimonio en "C. Matrimonio"');
						return false;
					}
					
					if((displayHijo != 'none') && (cnDZCargados == 0)){
						alert('Debe ingresar el certificado de Nacimiento de su hijo en "C. Nacimiento"');
						return false;
					}
					if((djDZCargados + f22DZCargados + arDZCargados + ccpDZCargados + crDZCargados + cnDZCargados + cmDZCargados + otDZCargados + lqDZCargados) > 0){
						validaciones = true
					}
				}else{/* hubo una falla al subir archivos la primera vez entonces y necesita validar algunos Dz */
					var arrayCantJson = JSON.parse($("#json_cantidad_faltantes").val());
		            for (var i in arrayCantJson){
		                if (arrayCantJson.hasOwnProperty(i)) {
		                	var DzIniRevision = arrayCantJson[i].tipo;
		                	var cantidad = parseInt(arrayCantJson[i].cantidad);
		                	var txtArchivo = 'archivo';
							if(cantidad > 1)
								txtArchivo = 'archivos';
		                	if(DzIniRevision == '1'){
			                	if(djDZCargados < cantidad){
					    			alert('Debe ingresar su declaración en "D. Jurada"');
					    			return false;
					    		}
				    		}
							if(DzIniRevision == '2'){
			                	if(f22DZCargados < cantidad){
			                		var confirmaMenos = window.confirm('Antes intent\u00F3 subir '+cantidad+' '+txtArchivo+'en F.22, ¿Esta seguro?');
									if(!confirmaMenos)return false;
					    		}
				    		}
				    		if(DzIniRevision == '3'){
			                	if(arDZCargados < cantidad){
			                		var confirmaMenos = window.confirm('Antes intent\u00F3 subir '+cantidad+' '+txtArchivo+'en A. Retenedores, ¿Esta seguro?');
									if(!confirmaMenos)return false;
					    		}
				    		}
				    		if(DzIniRevision == '4'){
			                	if(ccpDZCargados < cantidad){
			                		var confirmaMenos = window.confirm('Antes intent\u00F3 subir '+cantidad+' '+txtArchivo+'en C. Cotizaciones, ¿Esta seguro?');
									if(!confirmaMenos)return false;
					    		}
				    		}
				    		if(DzIniRevision == '5'){
			                	if(crDZCargados < cantidad){
			                		var confirmaMenos = window.confirm('Antes intent\u00F3 subir '+cantidad+' '+txtArchivo+'en C. Remuneraciones, ¿Esta seguro?');
									if(!confirmaMenos)return false;
					    		}
				    		}
				    		if(DzIniRevision == '6'){
					    		if(cnDZCargados == 0){
					    			/*si entra aqui significa que si o si es mandatory*/
					    			alert('Debe ingresar el certificado de Nacimiento de su hijo en "C. Nacimiento"');
					    			return false;
					    		}
					    	}
					    	if(DzIniRevision == '7'){
					    		if(cmDZCargados == 0){
					    			/*si entra aqui significa que si o si es mandatory*/
					    			alert('Debe ingresar el certificado de Matrimonio en "C. Matrimonio"');
					    			return false;
					    		}
					    	}
					    	if(DzIniRevision == '8'){
			                	if(otDZCargados < cantidad){
			                		var confirmaMenos = window.confirm('Antes intent\u00F3 subir '+cantidad+' '+txtArchivo+' en Otros, ¿Esta seguro?');
									if(!confirmaMenos)return false;
					    		}
				    		}
					    	if(DzIniRevision == '9'){
			                	if(lqDZCargados < cantidad){
			                		var confirmaMenos = window.confirm('Antes intent\u00F3 subir '+cantidad+' '+txtArchivo+' en Liquidaciones, ¿Esta seguro?');
									if(!confirmaMenos)return false;
					    		}
				    		}
		                }
		            }// fin del for
					validaciones = true;
				}//end else revisado
				if(!validaciones){
					abrirSelectorArchivosTabActiva();
					return false;
				}else{
					iniciarIntentoCarga(function(codCargaIntento){
						asignarIntentoAUrls(codCargaIntento, [djDZ, f22DZ, arDZ, ccpDZ, crDZ, cnDZ, cmDZ, otrosDZ, lqDZ]);
						$("#DIV_UPLOADS").addClass('hidden');
						$("#DIV_LOADER").removeClass('hidden');
						var modo_debug = $("#modo_debug").val();
						if(modo_debug == 'S'){
							inicioLog();
						}
						var totalArchivos = djDZCargados + f22DZCargados + arDZCargados + ccpDZCargados + crDZCargados + cnDZCargados + cmDZCargados + otDZCargados + lqDZCargados;
						iniciarSeguimientoSubida(totalArchivos);
						subirDZ(djDZ,tp_cod_dj);
						subirDZ(f22DZ,tp_f22);
						subirDZ(arDZ,tp_ar);
						subirDZ(ccpDZ,tp_ccp);
						subirDZ(crDZ,tp_cr);
						subirDZ(cnDZ,tp_cn);
						subirDZ(cmDZ,tp_cm);
						subirDZ(otrosDZ,tp_otros);
						subirDZ(lqDZ,tp_lq);
					});
				}
			}//end if confirmacion
			
		}// end if acepta condiciones checked
		else{
			alert('Debe aceptar las condiciones');
			$('#acepta_condiciones').focus();
			return false;
		}
	});
});

subidos = '';
var archivosPendientes = {};
var ultimoDetalleErrorSubida = '';
var estadoSubida = {
	totalEsperado: 0,
	completados: 0,
	confirmacionEjecutada: false,
	huboError: false,
	timeoutId: null
};

function iniciarIntentoCarga(callback){
	var codActual = $("#cod_carga_intento").val();
	if(codActual !== ''){
		callback(codActual);
		return;
	}
	$.ajax({
		url: 'ajax_iniciar_intento.php',
		type: 'POST',
		dataType: 'json',
		data: {
			cod_dj: $("#cod_dj").val(),
			cod_pers: $("#cod_pers").val()
		},
		success: function(respuesta){
			if(!respuesta || !respuesta.ok){
				alert(respuesta && respuesta.mensaje ? respuesta.mensaje : 'No fue posible iniciar el intento de carga.');
				return;
			}
			$("#cod_carga_intento").val(respuesta.cod_carga_intento);
			$("#numero_intento_carga").val(respuesta.numero_intento);
			callback(respuesta.cod_carga_intento);
		},
		error: function(){
			alert('No fue posible iniciar el intento de carga. Recargue la pagina e intente nuevamente.');
		}
	});
}

function asignarIntentoAUrls(codCargaIntento, dropzones){
	for(var i = 0; i < dropzones.length; i++){
		var urlBase = dropzones[i].options.url.replace(/&cod_carga_intento=[^&]*/g, '');
		dropzones[i].options.url = urlBase + '&cod_carga_intento=' + encodeURIComponent(codCargaIntento);
	}
}

function iniciarSeguimientoSubida(totalEsperado){
	if(estadoSubida.timeoutId){
		clearTimeout(estadoSubida.timeoutId);
	}
	ultimoDetalleErrorSubida = '';
	estadoSubida.totalEsperado = totalEsperado;
	estadoSubida.completados = 0;
	estadoSubida.confirmacionEjecutada = false;
	estadoSubida.huboError = false;
	estadoSubida.timeoutId = setTimeout(function() {
		if(!estadoSubida.confirmacionEjecutada && estadoSubida.completados < estadoSubida.totalEsperado){
			estadoSubida.huboError = true;
			mostrarDetalleErrorSubida('UPLOAD_TIMEOUT');
			$("#DIV_LOADER").addClass('hidden');
			$("#DIV_ERROR").removeClass('hidden');
		}
	}, 45000);
}

function registrarFinSubida(){
	if(estadoSubida.totalEsperado === 0 || estadoSubida.confirmacionEjecutada){
		return;
	}
	estadoSubida.completados++;
	if(estadoSubida.completados >= estadoSubida.totalEsperado){
		if(estadoSubida.timeoutId){
			clearTimeout(estadoSubida.timeoutId);
			estadoSubida.timeoutId = null;
		}
		estadoSubida.confirmacionEjecutada = true;
		if(estadoSubida.huboError || ultimoDetalleErrorSubida !== ''){
			$("#DIV_LOADER").addClass('hidden');
			$("#DIV_ERROR").removeClass('hidden');
			return;
		}
		confirmar_archivos();
	}
}

function detenerSeguimientoSubida(){
	if(estadoSubida.timeoutId){
		clearTimeout(estadoSubida.timeoutId);
		estadoSubida.timeoutId = null;
	}
}

function ocultarTabsAll(){
	$("#liDj").addClass('hidden');
	$("#liF22").addClass('hidden');
	$("#liAr").addClass('hidden');
	$("#liCcp").addClass('hidden');
	$("#liCr").addClass('hidden');
	$("#liCn").addClass('hidden');
	$("#liCm").addClass('hidden');
	$("#liOt").addClass('hidden');
	$("#liLq").addClass('hidden');
	
	quitarTabsActivos();
}

function quitarTabsActivos(){

	$("#liDj").removeClass('active');
	$("#liF22").removeClass('active');
	$("#liAr").removeClass('active');
	$("#liCcp").removeClass('active');
	$("#liCr").removeClass('active');
	$("#liCn").removeClass('active');
	$("#liCm").removeClass('active');
	$("#liOt").removeClass('active');
	$("#liLq").removeClass('active');

	$("#tabDj").removeClass('active');
	$("#tabDj").removeClass('in');
	
	$("#tabF22").removeClass('active');
	$("#tabF22").removeClass('in');
	
	$("#tabAr").removeClass('active');
	$("#tabAr").removeClass('in');
	
	$("#tabCcp").removeClass('active');
	$("#tabCcp").removeClass('in');
	
	$("#tabCr").removeClass('active');
	$("#tabCr").removeClass('in');
	
	$("#tabCn").removeClass('active');
	$("#tabCn").removeClass('in');
	
	$("#tabCm").removeClass('active');
	$("#tabCm").removeClass('in');
	
	$("#tabOt").removeClass('active');
	$("#tabOt").removeClass('in');
	
	$("#tabLq").removeClass('active');
	$("#tabLq").removeClass('in');
}

function subirDZ (dz,tp) {
	var archivosSeleccionados = obtenerArchivosPendientes(tp);
	if(archivosSeleccionados.length > 0){
		for (var i = 0; i < archivosSeleccionados.length; i++) {
			subirArchivoManual(dz, archivosSeleccionados[i]);
		}
	}
}

function normalizarRespuestaUpload(response){
	if(typeof response === "string"){
		return response.trim().replace(/^"+|"+$/g, '');
	}
	return response;
}

function subirArchivoManual(dz, file){
	if(!file || file.status === Dropzone.ERROR || file.status === Dropzone.CANCELED){
		registrarFinSubida();
		return;
	}
	if(file._subidaManualIniciada){
		return;
	}
	file._subidaManualIniciada = true;
	file.status = Dropzone.UPLOADING;
	dz.emit("processing", file);

	var formData = new FormData();
	formData.append("file", file, file.name);

	$.ajax({
		url: dz.options.url,
		type: "POST",
		data: formData,
		processData: false,
		contentType: false,
		cache: false,
		timeout: 45000,
		success: function(response) {
			var respuesta = normalizarRespuestaUpload(response);
			if(respuesta === 'existoso'){
				file.status = Dropzone.SUCCESS;
				dz.emit("success", file, response);
			}else{
				estadoSubida.huboError = true;
				mostrarDetalleErrorSubida(respuesta || 'UPLOAD_NOT_CONFIRMED');
				file.status = Dropzone.ERROR;
				dz.emit("error", file, {message: respuesta || "La subida no fue confirmada por el servidor."}, {status: 200, statusText: respuesta || "UPLOAD_NOT_CONFIRMED"});
			}
			dz.emit("complete", file);
		},
		error: function(xhr, statusText) {
			estadoSubida.huboError = true;
			mostrarDetalleErrorSubida(statusText || 'UPLOAD_AJAX_ERROR');
			file.status = Dropzone.ERROR;
			dz.emit("error", file, {message: "Error al subir el archivo."}, xhr || {status: 0, statusText: statusText || "ERROR"});
			dz.emit("complete", file);
		}
	});
}

function mostrarDetalleErrorSubida(detalle){
	if(!detalle){
		return;
	}
	ultimoDetalleErrorSubida = detalle;
	var mensajeBase = 'La administraci\u00F3n fondos de cr\u00E9dito se contactara con usted, recuerde mantener actualizado su correo electr\u00F3nico y tel\u00E9fonos.';
	$("#DIV_ERROR p.title").text(mensajeBase + ' C\u00F3digo: ' + detalle);
}

function obtenerLlaveArchivo(file){
	if(file.upload && file.upload.uuid){
		return file.upload.uuid;
	}
	return [file.name, file.size, file.lastModified || ''].join('||');
}

function registrarArchivoPendiente(tp, file){
	if(!archivosPendientes[tp]){
		archivosPendientes[tp] = [];
	}
	var llaveArchivo = obtenerLlaveArchivo(file);
	for (var i = 0; i < archivosPendientes[tp].length; i++) {
		if(obtenerLlaveArchivo(archivosPendientes[tp][i]) == llaveArchivo){
			return;
		}
	}
	archivosPendientes[tp].push(file);
}

function quitarArchivoPendiente(tp, file){
	if(!archivosPendientes[tp]){
		return;
	}
	var llaveArchivo = obtenerLlaveArchivo(file);
	archivosPendientes[tp] = archivosPendientes[tp].filter(function(item) {
		return obtenerLlaveArchivo(item) != llaveArchivo;
	});
}

function obtenerArchivosPendientes(tp){
	if(!archivosPendientes[tp]){
		return [];
	}
	return archivosPendientes[tp].filter(function(file) {
		return file.status !== Dropzone.ERROR && file.status !== Dropzone.CANCELED;
	});
}

function contarArchivosPendientes(tp){
	return obtenerArchivosPendientes(tp).length;
}

function abrirSelectorArchivosTabActiva(){
	var tabActiva = $(".tab-pane.active");
	if(tabActiva.length === 0){
		tabActiva = $("#tabDj");
	}
	var selectorDropzone = tabActiva.find(".dropzone").first();
	if(selectorDropzone.length === 0){
		alert('Debe ingresar al menos un archivo en alguna de las opciones disponibles.');
		return;
	}
	try{
		var dz = Dropzone.forElement("#"+selectorDropzone.attr("id"));
		if(dz.hiddenFileInput){
			dz.hiddenFileInput.click();
			return;
		}
	}catch(e){
		console.log('No se pudo abrir el selector de archivos', e);
	}
	alert('Debe ingresar al menos un archivo en alguna de las opciones disponibles.');
}

function enviar_mail_confirmado(){
	cod_dj = $("#cod_dj").val();
	cod_pers = $("#cod_pers").val();
	
	parametros = {                   
			"cod_dj" : cod_dj,
			"cod_pers" : cod_pers
		};
	url = "ajax_confirmacion_dj.php";
	$.ajax({    
		url: url,
		data: parametros,
		timeout: 15000,
		error: function() {
			console.log('No se pudo enviar el correo de confirmacion');
		}
	});
}

function limpiarDz(){
	var djDZ = Dropzone.forElement("#dj");
	var f22DZ = Dropzone.forElement("#f22");
	var arDZ = Dropzone.forElement("#ar");
	var ccpDZ = Dropzone.forElement("#ccp");
	var crDZ = Dropzone.forElement("#cr");
	var cnDZ = Dropzone.forElement("#cn");
	var cmDZ = Dropzone.forElement("#cm");
	var otrosDZ = Dropzone.forElement("#otros");
	var lqDZ = Dropzone.forElement("#lq");
	
	djDZ.removeAllFiles();
	f22DZ.removeAllFiles();
	arDZ.removeAllFiles();
	ccpDZ.removeAllFiles();
	crDZ.removeAllFiles();
	cnDZ.removeAllFiles();
	cmDZ.removeAllFiles();
	otrosDZ.removeAllFiles();
	lqDZ.removeAllFiles();
	archivosPendientes = {};
}

function confirmar_archivos(){
	var json_log = $("#json_debug").val();
	var cod_dj = $("#cod_dj").val();
	var cod_pers = $("#cod_pers").val();
	var parametros = { "json_log" : json_log,"cod_dj":cod_dj,"cod_pers":cod_pers,"cod_carga_intento":$("#cod_carga_intento").val() };
	url = "ajax_confirmar_archivos.php";
	$.ajax({    
		url: url,
		data: parametros,
		timeout: 15000,
        
		success: function(data)             
		{	data = data.trim();
			detenerSeguimientoSubida();
			if(data == 'CORRECTO'){
				console.log('confirmar archivos fue correcto');
				$("#DIV_LOADER").addClass('hidden');
				$("#DIV_EXITO").removeClass('hidden');
				enviar_mail_confirmado();
			}else if(data != 'CORRECTO'){
				var intentos = parseInt($("#intentos").val())+1;
				$("#intentos").val(intentos);
				if(intentos > 3){
					mail_error();
					return;
				}
				limpiarDz();
				ocultarTabsAll();
				$('#revisado').val(data);
				
				$("#DIV_LOADER").addClass('hidden');
				$("#DIV_UPLOADS").removeClass('hidden');
				
				var mensaje = '<p> Hubo un problema al tratar de subir los siguientes archivos, por favor re intente subir los archivos que detallamos a continuaci\u00F3n: </p>';
				var arrayRevisionJson = JSON.parse(data);
				$("#json_cantidad_faltantes").val('');// se vacia para partir de zero
				$("#json_debug").val(''); //log se vacia para iniciar uno nuevo
				var objCantidadXTipo = new Object();
				for (var i in arrayRevisionJson){
	                // Controlando que json realmente tenga esa propiedad
	                if (arrayRevisionJson.hasOwnProperty(i)) {
	               
	                    var DzIniRevision = arrayRevisionJson[i].TIPO;
	                    var nomArchivoRevision = arrayRevisionJson[i].NOMBRE;
                    	
	                    if($("#json_cantidad_faltantes").val().length == 0){
	                    	/*significa que es la primera iteracion por ende hay que armar el array*/
	                    	var arrayCantJson = []; 
				            objCantidadXTipo.cantidad = 1;
				            objCantidadXTipo.tipo = DzIniRevision;
				            arrayCantJson.push(objCantidadXTipo);

			    		}else if($("#json_cantidad_faltantes").val().length > 0){ 
		    				var arrayCantJson = JSON.parse($("#json_cantidad_faltantes").val());
		    				var encontrado = false;
				            for (var e in arrayCantJson){
				                if (arrayCantJson.hasOwnProperty(e)) {
				                	if(arrayCantJson[e].tipo == DzIniRevision){ 
				                        arrayCantJson[e].cantidad = parseInt(arrayCantJson[e].cantidad)+1;
				                        encontrado = true;
				                    }
				                }
				            }
				            if(!encontrado){
		                    	objCantidadXTipo.cantidad = 1;
					            objCantidadXTipo.tipo = DzIniRevision;
					            arrayCantJson.push(objCantidadXTipo);
		                    }
			    		}
			    		$("#json_cantidad_faltantes").val(JSON.stringify(arrayCantJson));
			    		
				    	if(DzIniRevision == '1'){
				    		quitarTabsActivos();
				    		$("#liDj").removeClass('hidden');
				    		$("#liDj").addClass('active');
				    		$("#tabDj").addClass('active');
				    		$("#tabDj").addClass('in');

				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarla en "D. Jurada" </p>';
				    	}
				    	if(DzIniRevision == '2'){
				    		quitarTabsActivos();
				    		$("#liF22").removeClass('hidden');
				    		$("#liF22").addClass('active');
				    		$("#tabF22").addClass('active');
				    		$("#tabF22").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "F. 22" </p>';
				    	}
				    	if(DzIniRevision == '3'){
				    		quitarTabsActivos();
				    		$("#liAr").removeClass('hidden');
				    		$("#liAr").addClass('active');
				    		$("#tabAr").addClass('active');
				    		$("#tabAr").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "A. Retenedores" </p>';
				    	}
				    	if(DzIniRevision == '4'){
				    		quitarTabsActivos();
				    		$("#liCcp").removeClass('hidden');
				    		$("#liCcp").addClass('active');
				    		$("#tabCcp").addClass('active');
				    		$("#tabCcp").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "C. Cotizaciones" </p>';
				    	}
				    	if(DzIniRevision == '5'){
				    		quitarTabsActivos();
				    		$("#liCr").removeClass('hidden');
				    		$("#liCr").addClass('active');
				    		$("#tabCr").addClass('active');
				    		$("#tabCr").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "C. Remuneraciones" </p>';
				    	}
				    	if(DzIniRevision == '6'){
				    		quitarTabsActivos();
				    		$("#liCn").removeClass('hidden');
				    		$("#liCn").addClass('active');
				    		$("#tabCn").addClass('active');
				    		$("#tabCn").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "C. Nacimiento" </p>';
				    	}
				    	if(DzIniRevision == '7'){
				    		quitarTabsActivos();
				    		$("#liCm").removeClass('hidden');
				    		$("#liCm").addClass('active');
				    		$("#tabCm").addClass('active');
				    		$("#tabCm").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "C. Matrimonio" </p>';
				    	}
				    	if(DzIniRevision == '8'){
				    		quitarTabsActivos();
				    		$("#liOt").removeClass('hidden');
				    		$("#liOt").addClass('active');
				    		$("#tabOt").addClass('active');
				    		$("#tabOt").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "Otros" </p>';
				    	}
				    	if(DzIniRevision == '9'){
				    		quitarTabsActivos();
				    		$("#liLq").removeClass('hidden');
				    		$("#liLq").addClass('active');
				    		$("#tabLq").addClass('active');
				    		$("#tabLq").addClass('in');
				    		mensaje = mensaje+'<p>'+nomArchivoRevision+', ingresarlo en "Liquidaciones" </p>';
				    	}
	                }
	            }
				
				$("#modal-titulo").html('Error en la subida');
				$(".modal-body").html(mensaje);
				$('#myModal').modal('show');
			}
		},
		error: function() {
			detenerSeguimientoSubida();
			$("#DIV_LOADER").addClass('hidden');
			$("#DIV_ERROR").removeClass('hidden');
		}
	});
}

function addListJson(tp,nomArchivo,peso){
	var agregadoTp = false;
	var obj = new Object();

	if($("#json_debug").val().length > 0){
		var arrayJson = JSON.parse($("#json_debug").val());
		for (var i in arrayJson){
			if (arrayJson.hasOwnProperty(i)) {
				if((arrayJson[i].tipoArchivo == tp) && (arrayJson[i].nomArchivo == nomArchivo)){
					$("#json_debug").val(JSON.stringify(arrayJson));
					return;
				}
			}
		}
		obj.tipoArchivo = tp;
        obj.nomArchivo = nomArchivo;
        obj.peso = peso;
        arrayJson.push(obj);
	}else if($("#json_debug").val().length == 0){
        var arrayJson = []; 
        obj.tipoArchivo = tp;
        obj.nomArchivo = nomArchivo;
        obj.peso = peso;
        arrayJson.push(obj);
    }

    $("#json_debug").val(JSON.stringify(arrayJson));
}

function removeListJson(tp,nomArchivo){
	var arrayJson = JSON.parse($("#json_debug").val());

	for (var i in arrayJson){
		if (arrayJson.hasOwnProperty(i)) {
			if((arrayJson[i].tipoArchivo == tp) && (arrayJson[i].nomArchivo == nomArchivo)){
                arrayJson.splice(i,1);
            }
		}	
	}

	$("#json_debug").val(JSON.stringify(arrayJson));
}

function inicioLog(){
	var json_log = $("#json_debug").val();
	var cod_dj = $("#cod_dj").val();
	var cod_pers = $("#cod_pers").val();
	var parametros = { "json_log" : json_log,"cod_dj":cod_dj,"cod_pers":cod_pers };
	url = "ajax_inicio_log.php";

	$.ajax({    
		url: url,
		data: parametros,
		timeout: 5000
	});
}

function agregarAlLog(tp,file,error='',xhr=''){
	var fileName = file.name;
	var peso = file.size;
	var obs = 'Subida exitosa segun dropzone';
	/* si no vacio error, siempre xhr vendra con data*/
	if(error != ''){
		obs = error.message+' xhr status: '+xhr.status+' '+xhr.statusText;
	}
	//console.log(tp+' '+fileName+' '+obs+' error:'+error);
	var cod_dj = $("#cod_dj").val();
	var cod_pers = $("#cod_pers").val();
	
	var parametros = { 
		"obs" : obs,
		"cod_dj":cod_dj,
		"cod_pers":cod_pers,
		"peso":peso,
		"nom_archivo":fileName,
		"tipo_archivo":tp
	};

	url = "ajax_extra_log.php";

	$.ajax({    
		url: url,
		data: parametros,
		timeout: 5000
	});
}

function mail_error(){
	cod_dj = $("#cod_dj").val();
	cod_pers = $("#cod_pers").val();
	
	parametros = {                   
			"cod_dj" : cod_dj,
			"cod_pers" : cod_pers
		};
	url = "ajax_mail_error.php";
	$.ajax({    
		url: url,
		data: parametros,
		timeout: 15000,
        
		success: function(data)             
		{	
			detenerSeguimientoSubida();
			$("#DIV_LOADER").addClass('hidden');
			$("#DIV_ERROR").removeClass('hidden');
		},
		error: function() {
			detenerSeguimientoSubida();
			$("#DIV_LOADER").addClass('hidden');
			$("#DIV_ERROR").removeClass('hidden');
		}
	});
	return false;	
}
