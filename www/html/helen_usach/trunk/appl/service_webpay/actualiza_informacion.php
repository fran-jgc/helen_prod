<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
require_once ("../../appl.ini");
require_once ("funciones.php");

class w_webpay_cupon {
	function w_webpay_cupon() {
		
		valida_inicio();
		$cod_deudor = session::get('COD_DEUDOR');
		
		$t = new Template_appl("actualiza_informacion.html");
		
		$ws_helen = new wakeUp_helen();
		$result = $ws_helen->datos_direccion_p($cod_deudor);

		$t->setVar("COD_DIRECCION_PARTICULAR", $result[0]['COD_DIRECCION_PARTICULAR']);
		$t->setVar("DIRECCION_PARTICULAR", $result[0]['DIRECCION_PARTICULAR']);
		$t->setVar("FECHA_DIRECCION_PARTICULAR", $result[0]['FECHA_DIRECCION_PARTICULAR']);
		$t->setVar("COD_COMUNA_PARTICULAR", $result[0]['COD_COMUNA_PARTICULAR']);
		$t->setVar("COD_CIUDAD_PARTICULAR", $result[0]['COD_CIUDAD_PARTICULAR']);
		$t->setVar("COD_REGION_PARTICULAR", $result[0]['COD_REGION_PARTICULAR']);
		$t->setVar("COD_PAIS_PARTICULAR", $result[0]['COD_PAIS_PARTICULAR']);
		$t->setVar("EMAIL_PARTICULAR", $result[0]['EMAIL_PARTICULAR']);
		$t->setVar("EMAIL_PARTICULAR_H", $result[0]['EMAIL_PARTICULAR_H']);
		$t->setVar("TELEFONO_PARTICULAR", $result[0]['TELEFONO_PARTICULAR']);
		$t->setVar("CELULAR_PARTICULAR", $result[0]['CELULAR_PARTICULAR']);
		$t->setVar("DIRECCION_PARTICULAR_H", $result[0]['DIRECCION_PARTICULAR_H']);
		$t->setVar("COD_COMUNA_PARTICULAR_H", $result[0]['COD_COMUNA_PARTICULAR_H']);
		$t->setVar("COD_CIUDAD_PARTICULAR_H", $result[0]['COD_CIUDAD_PARTICULAR_H']);
		$t->setVar("COD_REGION_PARTICULAR_H", $result[0]['COD_REGION_PARTICULAR_H']);
		$t->setVar("COD_PAIS_PARTICULAR_H", $result[0]['COD_PAIS_PARTICULAR_H']);
		$t->setVar("TELEFONO_PARTICULAR_H", $result[0]['TELEFONO_PARTICULAR_H']);
		$t->setVar("CELULAR_PARTICULAR_H", $result[0]['CELULAR_PARTICULAR_H']);
		$t->setVar("ES_VALIDA_PARTICULAR", $result[0]['ES_VALIDA_PARTICULAR']);
		$t->setVar("EXT_COMUNA_PARTICULAR", $result[0]['EXT_COMUNA_PARTICULAR']);
		$t->setVar("EXT_CIUDAD_PARTICULAR", $result[0]['EXT_CIUDAD_PARTICULAR']);
		$t->setVar("EXT_REGION_PARTICULAR", $result[0]['EXT_REGION_PARTICULAR']);
		$t->setVar("DISPLAY_PAIS_CHILE_PARTICULAR", $result[0]['DISPLAY_PAIS_CHILE_PARTICULAR']);
		$t->setVar("DISPLAY_EXTRANJERO_PARTICULAR", $result[0]['DISPLAY_EXTRANJERO_PARTICULAR']);
		
		$pais = '<select name="COD_PAIS_PARTICULAR_0" id="COD_PAIS_PARTICULAR_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$pais.='<option value="" label=""></option><option value="400">AFGANISTAN</option><option value="401">ALBANIA</option><option value="49">ALEMANIA</option><option value="402">ALTO VOLTA</option><option value="471">ANDORRA</option><option value="244">ANGOLA</option><option value="1264">ANGUILLA</option><option value="450">ANTARTIDA</option><option value="389">ANTIGUA REPUBLICA YUGOSLAVA DE MACEDONIA</option><option value="403">ANTIGUA Y BARBUDA</option><option value="599">ANTILLAS HOLANDESAS</option><option value="966">ARABIA SAUDI</option><option value="213">ARGELIA</option><option value="54">ARGENTINA</option><option value="374">ARMENIA</option><option value="404">ARUBA</option><option value="61">AUSTRALIA</option><option value="43">AUSTRIA</option><option value="994">AZERBAIYAN</option><option value="405">BAHAMAS</option><option value="973">BAHREIN</option><option value="880">BANGLADESH</option><option value="406">BARBADOS</option><option value="32">BELGICA</option><option value="501">BELICE</option><option value="229">BENIN</option><option value="407">BERMUDA</option><option value="408">BIELORRUSIA</option><option value="591">BOLIVIA</option><option value="979">BOSNIA Y HERZEGOVINA </option><option value="267">BOTSWANA</option><option value="451">BOUVET ISLANDS</option><option value="55">BRASIL</option><option value="673">BRUNEI DARUSSALAM </option><option value="359">BULGARIA</option><option value="226">BURKINA FASO</option><option value="452">BURMA</option><option value="257">BURUNDI</option><option value="409">BUTAN</option><option value="238">CABO VERDE</option><option value="855">CAMBOYA</option><option value="237">CAMERUN</option><option value="10">CANADA</option><option value="453">CANTON AND ENDERBURY ISLANDS</option><option value="235">CHAD</option><option value="42">CHECOSLOVAQUIA</option><option value="56" selected="">CHILE</option><option value="86">CHINA</option><option value="357">CHIPRE</option><option value="446">CIUDAD ESTADO DEL VATICANO</option><option value="57">COLOMBIA</option><option value="410">COMOROS</option><option value="411">COREA DEL NORTE</option><option value="82">COREA DEL SUR</option><option value="225">COSTA DE MARFIL</option><option value="506">COSTA RICA</option><option value="191">CROACIA</option><option value="53">CUBA</option><option value="45">DINAMARCA</option><option value="253">DJIBOUTI</option><option value="412">DOMINICA</option><option value="454">DRONNING MAUD LAND</option><option value="455">EAST TIMOR</option><option value="593">ECUADOR</option><option value="20">EGIPTO</option><option value="503">EL SALVADOR</option><option value="971">EMIRATOS ARABES UNIDOS</option><option value="291">ERITREA</option><option value="1421">ESLOVAQUIA</option><option value="386">ESLOVENIA</option><option value="34">ESPANA</option><option value="1">ESTADOS UNIDOS</option><option value="372">ESTONIA</option><option value="251">ETIOPIA</option><option value="456">FAEROE ISLANDS</option><option value="679">FIJI</option><option value="63">FILIPINAS</option><option value="358">FINLANDIA</option><option value="33">FRANCIA</option><option value="241">GABON</option><option value="220">GAMBIA</option><option value="995">GEORGIA</option><option value="1500">GEORGIA DEL SUR E ISLAS SANDWICH DEL SUR</option><option value="233">GHANA</option><option value="413">GIBRALTAR</option><option value="414">GRANADA</option><option value="30">GRECIA</option><option value="415">GROENLANDIA</option><option value="590">GUADALUPE</option><option value="671">GUAM</option><option value="502">GUATEMALA</option><option value="224">GUINEA</option><option value="240">GUINEA ECUATORIAL</option><option value="594">GUINEA FRANCESA</option><option value="245">GUINEA-BISSAU</option><option value="592">GUYANA</option><option value="509">HAITI</option><option value="469">HAWAI</option><option value="504">HONDURAS</option><option value="852">HONG KONG</option><option value="36">HUNGRIA</option><option value="91">INDIA</option><option value="62">INDONESIA</option><option value="98">IRAN</option><option value="964">IRAQ</option><option value="353">IRLANDA</option><option value="457">ISLA BOUVET</option><option value="1044">ISLA DE MAN</option><option value="1061">ISLA HEARD E ISLAS MCDONALD</option><option value="416">ISLA JOHNSTON Y SAND</option><option value="467">ISLA MIDWAY</option><option value="672">ISLA NAVIDAD</option><option value="417">ISLA WAKE</option><option value="354">ISLANDIA</option><option value="418">ISLAS CAIMAN</option><option value="420">ISLAS CHRISTMAS</option><option value="419">ISLAS COCOS</option><option value="682">ISLAS COOK</option><option value="421">ISLAS FALKLAND O MALVINAS</option><option value="298">ISLAS FAROE</option><option value="448">ISLAS FEROES</option><option value="449">ISLAS GILBERT</option><option value="422">ISLAS HEARD MCDONALD</option><option value="1670">ISLAS MARIANAS DEL NORTE</option><option value="692">ISLAS MARSHALL</option><option value="1001">ISLAS MENORES DE ESTADOS UNIDOS</option><option value="423">ISLAS NIUE</option><option value="424">ISLAS NORFOLK</option><option value="425">ISLAS PITCAIRN</option><option value="426">ISLAS SALOMON</option><option value="458">ISLAS SVALBARD Y JUAN MAYEN</option><option value="427">ISLAS TURCAS Y CAICOS</option><option value="428">ISLAS VIRGENES AMERICANAS</option><option value="429">ISLAS VIRGENES BRITANICAS</option><option value="430">ISLAS WALLIS Y FUTUNA</option><option value="1358">ISLAS ¿LAND</option><option value="972">ISRAEL</option><option value="39">ITALIA</option><option value="431">JAMAICA</option><option value="81">JAPON</option><option value="962">JORDANIA</option><option value="7">KAZAJSTAN</option><option value="254">KENIA</option><option value="996">KIRGUIZISTAN</option><option value="686">KIRIBATI</option><option value="965">KUWAIT</option><option value="432">LAO</option><option value="856">LAOS</option><option value="266">LESOTO</option><option value="371">LETONIA</option><option value="961">LIBANO</option><option value="231">LIBERIA</option><option value="218">LIBIA</option><option value="473">LIECHTENSTEIN</option><option value="370">LITUANIA</option><option value="352">LUXEMBURGO</option><option value="853">MACAO</option><option value="261">MADAGASCAR</option><option value="60">MALASIA</option><option value="265">MALAWI</option><option value="960">MALDIVAS</option><option value="223">MALI</option><option value="356">MALTA</option><option value="212">MARRUECOS</option><option value="596">MARTINICA</option><option value="230">MAURICIO</option><option value="222">MAURITANIA</option><option value="175">MAYOTTE</option><option value="52">MEXICO</option><option value="691">MICRONESIA</option><option value="373">MOLDAVIA</option><option value="468">MONACO</option><option value="433">MONGOLIA</option><option value="434">MONTSERRAT</option><option value="258">MOZAMBIQUE</option><option value="459">MUNDIAL</option><option value="95">MYANMAR</option><option value="978">NACIONALIDAD EXTRANJERA</option><option value="264">NAMIBIA</option><option value="674">NAURU</option><option value="977">NEPAL</option><option value="505">NICARAGUA</option><option value="227">NIGER</option><option value="234">NIGERIA</option><option value="683">NIUE</option><option value="47">NORUEGA</option><option value="687">NUEVA CALEDONIA</option><option value="64">NUEVA ZELANDA</option><option value="460">NUEVAS HEBRIDAS</option><option value="461">OCEANO INDICO, TERR. BRITANICO</option><option value="968">OMAN</option><option value="462">PACIFICO, ISLAS VARIAS DE EEUU</option><option value="31">PAISES BAJOS</option><option value="92">PAKISTAN</option><option value="680">PALAU</option><option value="507">PANAMA</option><option value="675">PAPUA NUEVA GUINEA </option><option value="595">PARAGUAY</option><option value="51">PERU</option><option value="435">POLINESIA FRANCESA</option><option value="48">POLONIA</option><option value="351">PORTUGAL</option><option value="470">PUERTO RICO</option><option value="974">QATAR</option><option value="44">REINO UNIDO</option><option value="236">REPUBLICA CENTROAFRICANA</option><option value="980">REPUBLICA CHECA</option><option value="242">REPUBLICA DEL CONGO</option><option value="243">REPUBLICA DEMOCRATICA DEL CONGO</option><option value="436">REPUBLICA DOMINICANA</option><option value="262">REUNION</option><option value="464">RHODESIA DEL SUR</option><option value="250">RUANDA</option><option value="40">RUMANIA</option><option value="437">RUSIA</option><option value="438">SAHARA OCCIDENTAL</option><option value="685">SAMOA</option><option value="684">SAMOA AMERICANA</option><option value="463">SAN CRISTOBAL Y NIEVES </option><option value="472">SAN MARINO</option><option value="508">SAN PEDRO Y MIQUELON</option><option value="439">SAN VICENTE Y LAS GRANADINAS</option><option value="440">SANTA HELENA </option><option value="441">SANTA LUCIA</option><option value="239">SANTO TOME Y PRINCIPE</option><option value="221">SENEGAL</option><option value="381">SERBIA Y MONTENEGRO</option><option value="248">SEYCHELLES</option><option value="232">SIERRA LEONA</option><option value="0">SIN INFORMACION</option><option value="65">SINGAPUR</option><option value="963">SIRIA</option><option value="442">SOMALIA</option><option value="94">SRI LANKA</option><option value="268">SUAZILANDIA</option><option value="27">SUDAFRICA</option><option value="249">SUDAN</option><option value="46">SUECIA</option><option value="41">SUIZA</option><option value="597">SURINAM</option><option value="79">SVALBARD Y JAN MAYEN</option><option value="689">TAHITI</option><option value="66">TAILANDIA</option><option value="886">TAIWAN</option><option value="992">TAJIKISTAN</option><option value="255">TANZANIA</option><option value="246">TERRITORIO OCEANICO DE LA INDIA BRITANICA</option><option value="970">TERRITORIOS PALESTINOS OCUPADOS</option><option value="1033">TERRITORIOS SURE¿OS DE FRANCIA</option><option value="670">TIMOR LESTE</option><option value="465">TIMOR ORIENTAL</option><option value="228">TOGO</option><option value="466">TOKELAU</option><option value="676">TONGA</option><option value="443">TRINIDAD Y TOBAGO</option><option value="216">TUNEZ</option><option value="993">TURKMENISTAN</option><option value="90">TURQUIA</option><option value="444">TUVALU</option><option value="445">UCRANIA</option><option value="256">UGANDA</option><option value="598">URUGUAY</option><option value="998">UZBEKISTAN</option><option value="678">VANUATU</option><option value="58">VENEZUELA</option><option value="447">VIETNAM</option><option value="681">WALLIS Y FUTUNA</option><option value="967">YEMEN</option><option value="38">YUGOSLAVIA</option><option value="260">ZAMBIA</option><option value="263">ZIMBABWE</option></select>';
		
		$t->setVar("PAIS", $pais);
		
		//REGION
		$region = '<select name="COD_REGION_PARTICULAR_0" id="COD_REGION_PARTICULAR_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$region.='<option value="" label="">';
		if($result[0]['COD_PAIS_PARTICULAR'] == 56){
			$result_region = $ws_helen->datos_region($result[0]['COD_PAIS_PARTICULAR']);
			$cadena = '';
			for($i=0;$i<count($result_region);$i++){
				if($result[0]['COD_REGION_PARTICULAR'] == $result_region[$i]['COD_REGION']){
					$cadena=$cadena.'<option value='.$result_region[$i]['COD_REGION'].' selected="">'.utf8_encode($result_region[$i]['NOM_REGION']).'</option>';
				}
				else{
					$cadena=$cadena.'<option value='.$result_region[$i]['COD_REGION'].'>'.utf8_encode($result_region[$i]['NOM_REGION']).'</option>';
				}
			}
			$region.=$cadena;
		}
		$fin_option = '</option></select>';
		$region.=$fin_option;
		$t->setVar("REGION", $region);
		
		
		
		
		//CIUDAD
		$ciudad = '<select name="COD_CIUDAD_PARTICULAR_0" id="COD_CIUDAD_PARTICULAR_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$ciudad.='<option value="" label="">';
		if($result[0]['COD_REGION_PARTICULAR'] != ''){
			$result_ciudad = $ws_helen->datos_ciudad($result[0]['COD_REGION_PARTICULAR']);
			$cadena = '';
			for($i=0;$i<count($result_ciudad);$i++){
				if($result[0]['COD_CIUDAD_PARTICULAR'] == $result_ciudad[$i]['COD_CIUDAD']){
					$cadena=$cadena.'<option value='.$result_ciudad[$i]['COD_CIUDAD'].' selected="">'.utf8_encode($result_ciudad[$i]['NOM_CIUDAD']).'</option>';
				}
				else{
					$cadena=$cadena.'<option value='.$result_ciudad[$i]['COD_CIUDAD'].'>'.utf8_encode($result_ciudad[$i]['NOM_CIUDAD']).'</option>';
				}
			}
			$ciudad.=$cadena;
		}
		$fin_option = '</option></select>';
		$ciudad.=$fin_option;
		$t->setVar("CIUDAD", $ciudad);
		///////////////////////////////////////////////
		
		
		//COMUNA
		$comuna = '<select name="COD_COMUNA_PARTICULAR_0" id="COD_COMUNA_PARTICULAR_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$comuna.='<option value="" label="">';
		if($result[0]['COD_CIUDAD_PARTICULAR'] != ''){
			$result_comuna = $ws_helen->datos_comuna($result[0]['COD_CIUDAD_PARTICULAR']);
			$cadena = '';
			for($i=0;$i<count($result_comuna);$i++){
				if($result[0]['COD_COMUNA_PARTICULAR'] == $result_comuna[$i]['COD_COMUNA']){
					$cadena=$cadena.'<option value='.$result_comuna[$i]['COD_COMUNA'].' selected="">'.utf8_encode($result_comuna[$i]['NOM_COMUNA']).'</option>';
				}
				else{
					$cadena=$cadena.'<option value='.$result_comuna[$i]['COD_COMUNA'].'>'.utf8_encode($result_comuna[$i]['NOM_COMUNA']).'</option>';
				}
			}
			$comuna.=$cadena;
		}
		$fin_option = '</option></select>';
		$comuna.=$fin_option;
		$t->setVar("COMUNA", $comuna);
		///////////////////////////////////////////////
		
		
		/*COMERCIAL*/
		$result = $ws_helen->datos_direccion_c($cod_deudor);
		
		
		$t->setVar("COD_DIRECCION_COMERCIAL", $result[0]['COD_DIRECCION_COMERCIAL']);
		$t->setVar("DIRECCION_COMERCIAL", $result[0]['DIRECCION_COMERCIAL']);
		$t->setVar("FECHA_DIRECCION_COMERCIAL", $result[0]['FECHA_DIRECCION_COMERCIAL']);
		$t->setVar("COD_COMUNA_COMERCIAL", $result[0]['COD_COMUNA_COMERCIAL']);
		$t->setVar("COD_CIUDAD_COMERCIAL", $result[0]['COD_CIUDAD_COMERCIAL']);
		$t->setVar("COD_REGION_COMERCIAL", $result[0]['COD_REGION_COMERCIAL']);
		$t->setVar("COD_PAIS_COMERCIAL", $result[0]['COD_PAIS_COMERCIAL']);
		$t->setVar("EMAIL_COMERCIAL", $result[0]['EMAIL_COMERCIAL']);
		$t->setVar("EMAIL_COMERCIAL_H", $result[0]['EMAIL_COMERCIAL_H']);
		$t->setVar("TELEFONO_COMERCIAL", $result[0]['TELEFONO_COMERCIAL']);
		$t->setVar("CELULAR_COMERCIAL", $result[0]['CELULAR_COMERCIAL']);
		$t->setVar("DIRECCION_COMERCIAL_H", $result[0]['DIRECCION_COMERCIAL_H']);
		$t->setVar("COD_COMUNA_COMERCIAL_H", $result[0]['COD_COMUNA_COMERCIAL_H']);
		$t->setVar("COD_CIUDAD_COMERCIAL_H", $result[0]['COD_CIUDAD_COMERCIAL_H']);
		$t->setVar("COD_REGION_COMERCIAL_H", $result[0]['COD_REGION_COMERCIAL_H']);
		$t->setVar("COD_PAIS_COMERCIAL_H", $result[0]['COD_PAIS_COMERCIAL_H']);
		$t->setVar("TELEFONO_COMERCIAL_H", $result[0]['TELEFONO_COMERCIAL_H']);
		$t->setVar("CELULAR_COMERCIAL_H", $result[0]['CELULAR_COMERCIAL_H']);
		$t->setVar("ES_VALIDA_COMERCIAL", $result[0]['ES_VALIDA_COMERCIAL']);
		$t->setVar("EXT_COMUNA_COMERCIAL", $result[0]['EXT_COMUNA_COMERCIAL']);
		$t->setVar("EXT_CIUDAD_COMERCIAL", $result[0]['EXT_CIUDAD_COMERCIAL']);
		$t->setVar("EXT_REGION_COMERCIAL", $result[0]['EXT_REGION_COMERCIAL']);
		$t->setVar("DISPLAY_PAIS_CHILE_COMERCIAL", $result[0]['DISPLAY_PAIS_CHILE_COMERCIAL']);
		$t->setVar("DISPLAY_EXTRANJERO_COMERCIAL", $result[0]['DISPLAY_EXTRANJERO_COMERCIAL']);
		
		$pais = '<select name="COD_PAIS_COMERCIAL_0" id="COD_PAIS_COMERCIAL_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$pais.='<option value="" label=""></option><option value="400">AFGANISTAN</option><option value="401">ALBANIA</option><option value="49">ALEMANIA</option><option value="402">ALTO VOLTA</option><option value="471">ANDORRA</option><option value="244">ANGOLA</option><option value="1264">ANGUILLA</option><option value="450">ANTARTIDA</option><option value="389">ANTIGUA REPUBLICA YUGOSLAVA DE MACEDONIA</option><option value="403">ANTIGUA Y BARBUDA</option><option value="599">ANTILLAS HOLANDESAS</option><option value="966">ARABIA SAUDI</option><option value="213">ARGELIA</option><option value="54">ARGENTINA</option><option value="374">ARMENIA</option><option value="404">ARUBA</option><option value="61">AUSTRALIA</option><option value="43">AUSTRIA</option><option value="994">AZERBAIYAN</option><option value="405">BAHAMAS</option><option value="973">BAHREIN</option><option value="880">BANGLADESH</option><option value="406">BARBADOS</option><option value="32">BELGICA</option><option value="501">BELICE</option><option value="229">BENIN</option><option value="407">BERMUDA</option><option value="408">BIELORRUSIA</option><option value="591">BOLIVIA</option><option value="979">BOSNIA Y HERZEGOVINA </option><option value="267">BOTSWANA</option><option value="451">BOUVET ISLANDS</option><option value="55">BRASIL</option><option value="673">BRUNEI DARUSSALAM </option><option value="359">BULGARIA</option><option value="226">BURKINA FASO</option><option value="452">BURMA</option><option value="257">BURUNDI</option><option value="409">BUTAN</option><option value="238">CABO VERDE</option><option value="855">CAMBOYA</option><option value="237">CAMERUN</option><option value="10">CANADA</option><option value="453">CANTON AND ENDERBURY ISLANDS</option><option value="235">CHAD</option><option value="42">CHECOSLOVAQUIA</option><option value="56" selected="">CHILE</option><option value="86">CHINA</option><option value="357">CHIPRE</option><option value="446">CIUDAD ESTADO DEL VATICANO</option><option value="57">COLOMBIA</option><option value="410">COMOROS</option><option value="411">COREA DEL NORTE</option><option value="82">COREA DEL SUR</option><option value="225">COSTA DE MARFIL</option><option value="506">COSTA RICA</option><option value="191">CROACIA</option><option value="53">CUBA</option><option value="45">DINAMARCA</option><option value="253">DJIBOUTI</option><option value="412">DOMINICA</option><option value="454">DRONNING MAUD LAND</option><option value="455">EAST TIMOR</option><option value="593">ECUADOR</option><option value="20">EGIPTO</option><option value="503">EL SALVADOR</option><option value="971">EMIRATOS ARABES UNIDOS</option><option value="291">ERITREA</option><option value="1421">ESLOVAQUIA</option><option value="386">ESLOVENIA</option><option value="34">ESPANA</option><option value="1">ESTADOS UNIDOS</option><option value="372">ESTONIA</option><option value="251">ETIOPIA</option><option value="456">FAEROE ISLANDS</option><option value="679">FIJI</option><option value="63">FILIPINAS</option><option value="358">FINLANDIA</option><option value="33">FRANCIA</option><option value="241">GABON</option><option value="220">GAMBIA</option><option value="995">GEORGIA</option><option value="1500">GEORGIA DEL SUR E ISLAS SANDWICH DEL SUR</option><option value="233">GHANA</option><option value="413">GIBRALTAR</option><option value="414">GRANADA</option><option value="30">GRECIA</option><option value="415">GROENLANDIA</option><option value="590">GUADALUPE</option><option value="671">GUAM</option><option value="502">GUATEMALA</option><option value="224">GUINEA</option><option value="240">GUINEA ECUATORIAL</option><option value="594">GUINEA FRANCESA</option><option value="245">GUINEA-BISSAU</option><option value="592">GUYANA</option><option value="509">HAITI</option><option value="469">HAWAI</option><option value="504">HONDURAS</option><option value="852">HONG KONG</option><option value="36">HUNGRIA</option><option value="91">INDIA</option><option value="62">INDONESIA</option><option value="98">IRAN</option><option value="964">IRAQ</option><option value="353">IRLANDA</option><option value="457">ISLA BOUVET</option><option value="1044">ISLA DE MAN</option><option value="1061">ISLA HEARD E ISLAS MCDONALD</option><option value="416">ISLA JOHNSTON Y SAND</option><option value="467">ISLA MIDWAY</option><option value="672">ISLA NAVIDAD</option><option value="417">ISLA WAKE</option><option value="354">ISLANDIA</option><option value="418">ISLAS CAIMAN</option><option value="420">ISLAS CHRISTMAS</option><option value="419">ISLAS COCOS</option><option value="682">ISLAS COOK</option><option value="421">ISLAS FALKLAND O MALVINAS</option><option value="298">ISLAS FAROE</option><option value="448">ISLAS FEROES</option><option value="449">ISLAS GILBERT</option><option value="422">ISLAS HEARD MCDONALD</option><option value="1670">ISLAS MARIANAS DEL NORTE</option><option value="692">ISLAS MARSHALL</option><option value="1001">ISLAS MENORES DE ESTADOS UNIDOS</option><option value="423">ISLAS NIUE</option><option value="424">ISLAS NORFOLK</option><option value="425">ISLAS PITCAIRN</option><option value="426">ISLAS SALOMON</option><option value="458">ISLAS SVALBARD Y JUAN MAYEN</option><option value="427">ISLAS TURCAS Y CAICOS</option><option value="428">ISLAS VIRGENES AMERICANAS</option><option value="429">ISLAS VIRGENES BRITANICAS</option><option value="430">ISLAS WALLIS Y FUTUNA</option><option value="1358">ISLAS ¿LAND</option><option value="972">ISRAEL</option><option value="39">ITALIA</option><option value="431">JAMAICA</option><option value="81">JAPON</option><option value="962">JORDANIA</option><option value="7">KAZAJSTAN</option><option value="254">KENIA</option><option value="996">KIRGUIZISTAN</option><option value="686">KIRIBATI</option><option value="965">KUWAIT</option><option value="432">LAO</option><option value="856">LAOS</option><option value="266">LESOTO</option><option value="371">LETONIA</option><option value="961">LIBANO</option><option value="231">LIBERIA</option><option value="218">LIBIA</option><option value="473">LIECHTENSTEIN</option><option value="370">LITUANIA</option><option value="352">LUXEMBURGO</option><option value="853">MACAO</option><option value="261">MADAGASCAR</option><option value="60">MALASIA</option><option value="265">MALAWI</option><option value="960">MALDIVAS</option><option value="223">MALI</option><option value="356">MALTA</option><option value="212">MARRUECOS</option><option value="596">MARTINICA</option><option value="230">MAURICIO</option><option value="222">MAURITANIA</option><option value="175">MAYOTTE</option><option value="52">MEXICO</option><option value="691">MICRONESIA</option><option value="373">MOLDAVIA</option><option value="468">MONACO</option><option value="433">MONGOLIA</option><option value="434">MONTSERRAT</option><option value="258">MOZAMBIQUE</option><option value="459">MUNDIAL</option><option value="95">MYANMAR</option><option value="978">NACIONALIDAD EXTRANJERA</option><option value="264">NAMIBIA</option><option value="674">NAURU</option><option value="977">NEPAL</option><option value="505">NICARAGUA</option><option value="227">NIGER</option><option value="234">NIGERIA</option><option value="683">NIUE</option><option value="47">NORUEGA</option><option value="687">NUEVA CALEDONIA</option><option value="64">NUEVA ZELANDA</option><option value="460">NUEVAS HEBRIDAS</option><option value="461">OCEANO INDICO, TERR. BRITANICO</option><option value="968">OMAN</option><option value="462">PACIFICO, ISLAS VARIAS DE EEUU</option><option value="31">PAISES BAJOS</option><option value="92">PAKISTAN</option><option value="680">PALAU</option><option value="507">PANAMA</option><option value="675">PAPUA NUEVA GUINEA </option><option value="595">PARAGUAY</option><option value="51">PERU</option><option value="435">POLINESIA FRANCESA</option><option value="48">POLONIA</option><option value="351">PORTUGAL</option><option value="470">PUERTO RICO</option><option value="974">QATAR</option><option value="44">REINO UNIDO</option><option value="236">REPUBLICA CENTROAFRICANA</option><option value="980">REPUBLICA CHECA</option><option value="242">REPUBLICA DEL CONGO</option><option value="243">REPUBLICA DEMOCRATICA DEL CONGO</option><option value="436">REPUBLICA DOMINICANA</option><option value="262">REUNION</option><option value="464">RHODESIA DEL SUR</option><option value="250">RUANDA</option><option value="40">RUMANIA</option><option value="437">RUSIA</option><option value="438">SAHARA OCCIDENTAL</option><option value="685">SAMOA</option><option value="684">SAMOA AMERICANA</option><option value="463">SAN CRISTOBAL Y NIEVES </option><option value="472">SAN MARINO</option><option value="508">SAN PEDRO Y MIQUELON</option><option value="439">SAN VICENTE Y LAS GRANADINAS</option><option value="440">SANTA HELENA </option><option value="441">SANTA LUCIA</option><option value="239">SANTO TOME Y PRINCIPE</option><option value="221">SENEGAL</option><option value="381">SERBIA Y MONTENEGRO</option><option value="248">SEYCHELLES</option><option value="232">SIERRA LEONA</option><option value="0">SIN INFORMACION</option><option value="65">SINGAPUR</option><option value="963">SIRIA</option><option value="442">SOMALIA</option><option value="94">SRI LANKA</option><option value="268">SUAZILANDIA</option><option value="27">SUDAFRICA</option><option value="249">SUDAN</option><option value="46">SUECIA</option><option value="41">SUIZA</option><option value="597">SURINAM</option><option value="79">SVALBARD Y JAN MAYEN</option><option value="689">TAHITI</option><option value="66">TAILANDIA</option><option value="886">TAIWAN</option><option value="992">TAJIKISTAN</option><option value="255">TANZANIA</option><option value="246">TERRITORIO OCEANICO DE LA INDIA BRITANICA</option><option value="970">TERRITORIOS PALESTINOS OCUPADOS</option><option value="1033">TERRITORIOS SURE¿OS DE FRANCIA</option><option value="670">TIMOR LESTE</option><option value="465">TIMOR ORIENTAL</option><option value="228">TOGO</option><option value="466">TOKELAU</option><option value="676">TONGA</option><option value="443">TRINIDAD Y TOBAGO</option><option value="216">TUNEZ</option><option value="993">TURKMENISTAN</option><option value="90">TURQUIA</option><option value="444">TUVALU</option><option value="445">UCRANIA</option><option value="256">UGANDA</option><option value="598">URUGUAY</option><option value="998">UZBEKISTAN</option><option value="678">VANUATU</option><option value="58">VENEZUELA</option><option value="447">VIETNAM</option><option value="681">WALLIS Y FUTUNA</option><option value="967">YEMEN</option><option value="38">YUGOSLAVIA</option><option value="260">ZAMBIA</option><option value="263">ZIMBABWE</option></select>';
		
		$t->setVar("PAIS_COMERCIAL", $pais);
		
		//REGION
		$region = '<select name="COD_REGION_COMERCIAL_0" id="COD_REGION_COMERCIAL_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$region.='<option value="" label="">';
		if($result[0]['COD_PAIS_COMERCIAL'] == 56){
			$result_region = $ws_helen->datos_region($result[0]['COD_PAIS_COMERCIAL']);
			$cadena = '';
			for($i=0;$i<count($result_region);$i++){
				if($result[0]['COD_REGION_COMERCIAL'] == $result_region[$i]['COD_REGION']){
					$cadena=$cadena.'<option value='.$result_region[$i]['COD_REGION'].' selected="">'.utf8_encode($result_region[$i]['NOM_REGION']).'</option>';
				}
				else{
					$cadena=$cadena.'<option value='.$result_region[$i]['COD_REGION'].'>'.utf8_encode($result_region[$i]['NOM_REGION']).'</option>';
				}
			}
			$region.=$cadena;
		}
		$fin_option = '</option></select>';
		$region.=$fin_option;
		$t->setVar("REGION_COMERCIAL", $region);
		
		
		
		
		//CIUDAD
		$ciudad = '<select name="COD_CIUDAD_COMERCIAL_0" id="COD_CIUDAD_COMERCIAL_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$ciudad.='<option value="" label="">';
		if($result[0]['COD_REGION_COMERCIAL'] != ''){
			$result_ciudad = $ws_helen->datos_ciudad($result[0]['COD_REGION_COMERCIAL']);
			$cadena = '';
			for($i=0;$i<count($result_ciudad);$i++){
				if($result[0]['COD_CIUDAD_COMERCIAL'] == $result_ciudad[$i]['COD_CIUDAD']){
					$cadena=$cadena.'<option value='.$result_ciudad[$i]['COD_CIUDAD'].' selected="">'.utf8_encode($result_ciudad[$i]['NOM_CIUDAD']).'</option>';
				}
				else{
					$cadena=$cadena.'<option value='.$result_ciudad[$i]['COD_CIUDAD'].'>'.utf8_encode($result_ciudad[$i]['NOM_CIUDAD']).'</option>';
				}
			}
			$ciudad.=$cadena;
		}
		$fin_option = '</option></select>';
		$ciudad.=$fin_option;
		$t->setVar("CIUDAD_COMERCIAL", $ciudad);
		///////////////////////////////////////////////
		
		
		//COMUNA
		$comuna = '<select name="COD_COMUNA_COMERCIAL_0" id="COD_COMUNA_COMERCIAL_0" onchange="load_drop_down_web(this);"  class="drop_down" style="width: 90px; border-image: none 100% / 1 / 0 stretch;">';
		$comuna.='<option value="" label="">';
		if($result[0]['COD_CIUDAD_COMERCIAL'] != ''){
			$result_comuna = $ws_helen->datos_comuna($result[0]['COD_CIUDAD_COMERCIAL']);
			$cadena = '';
			for($i=0;$i<count($result_comuna);$i++){
				if($result[0]['COD_COMUNA_COMERCIAL'] == $result_comuna[$i]['COD_COMUNA']){
					$cadena=$cadena.'<option value='.$result_comuna[$i]['COD_COMUNA'].' selected="">'.utf8_encode($result_comuna[$i]['NOM_COMUNA']).'</option>';
				}
				else{
					$cadena=$cadena.'<option value='.$result_comuna[$i]['COD_COMUNA'].'>'.utf8_encode($result_comuna[$i]['NOM_COMUNA']).'</option>';
				}
			}
			$comuna.=$cadena;
		}
		$fin_option = '</option></select>';
		$comuna.=$fin_option;
		$t->setVar("COMUNA_COMERCIAL", $comuna);
		///////////////////////////////////////////////
		
		if (!isset($_POST['b_enviar'])){
			print $t->toString();
		}
	}
	
	function save_direccion() {
		
		$cod_direccion = $_POST['COD_DIRECCION_PARTICULAR_0'];
		$direccion = $_POST['DIRECCION_PARTICULAR_0'];
		$pais = $_POST['COD_PAIS_PARTICULAR_0'];
		$region = $_POST['COD_REGION_PARTICULAR_0'];
		$ciudad = $_POST['COD_CIUDAD_PARTICULAR_0'];
		$comuna = $_POST['COD_COMUNA_PARTICULAR_0'];
		$mail = $_POST['EMAIL_PARTICULAR_0'];
		$telefono = $_POST['TELEFONO_PARTICULAR_0'];
		$celular = $_POST['CELULAR_PARTICULAR_0'];
		$cod_deudor = session::get('COD_DEUDOR');
		$tipo_direccion = 'P';
		
		if(($direccion != '')&&
			($pais != '')&&
			($region != '')&&
			($ciudad != '')&&
			($comuna != '')&&
			($mail != '')&&
			($telefono != '')&&
			($celular != '')){

			$ws_helen = new wakeUp_helen();
			$result = $ws_helen->actualiza_direccion($cod_direccion,$cod_deudor,$tipo_direccion,$direccion,$comuna,$ciudad,$pais,$mail,$telefono,$celular);
		
			if($result['COD_RESPUESTA'] == 100)
				header ('Location:'."error.php?error=2");
			else
				header ('Location:'."error.php?error=3");
		}
		
		$cod_direccion = $_POST['COD_DIRECCION_COMERCIAL_0'];
		$direccion = $_POST['DIRECCION_COMERCIAL_0'];
		$pais = $_POST['COD_PAIS_COMERCIAL_0'];
		$region = $_POST['COD_REGION_COMERCIAL_0'];
		$ciudad = $_POST['COD_CIUDAD_COMERCIAL_0'];
		$comuna = $_POST['COD_COMUNA_COMERCIAL_0'];
		$mail = $_POST['EMAIL_COMERCIAL_0'];
		$telefono = $_POST['TELEFONO_COMERCIAL_0'];
		$celular = $_POST['CELULAR_COMERCIAL_0'];
		$tipo_direccion = 'C';
				
		
		
		if(($direccion != '')&&
			($pais != '')&&
			($region != '')&&
			($ciudad != '')&&
			($comuna != '')&&
			($mail != '')&&
			($telefono != '')&&
			($celular != '')){

			$ws_helen = new wakeUp_helen();
			$result = $ws_helen->actualiza_direccion($cod_direccion,$cod_deudor,$tipo_direccion,$direccion,$comuna,$ciudad,$pais,$mail,$telefono,$celular);
		
			if($result['COD_RESPUESTA'] == 100)
				header ('Location:'."error.php?error=2");
			else
				header ('Location:'."error.php?error=3");
		}
			
		header ('Location:'."error.php?error=3");
		
	}
}

if (isset($_POST['b_enviar'])){
	$w = new w_webpay_cupon();
	$w->save_direccion();
}
else{
	new w_webpay_cupon();
}
?>