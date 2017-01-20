function bulkUploader(config){
	this.config = config;
	this.items = "";
	this.all = []
	var self = this;
	bulkUploader.prototype._init = function(){
		if (window.File && 
			window.FileReader && 
			window.FileList && 
			window.Blob ||
			typeof FileReader !== undefined) {		

			 var inputId = $("#"+this.config.form).find("input[type='file']").eq(0).attr("id"); 

			 document.getElementById(inputId).addEventListener("change", this._read, false);
			 document.getElementById(this.config.dragArea).addEventListener("dragover", function(e){ e.stopPropagation(); e.preventDefault(); }, false);
			 document.getElementById(this.config.dragArea).addEventListener("drop", this._dropFiles, false);
			 document.getElementById(this.config.form).addEventListener("submit", this._submit, false);
		} else
			console.log("Browser supports failed");
	}
	
	bulkUploader.prototype._submit = function(e){
		e.stopPropagation(); e.preventDefault();
		if($('#'+self.config.dragArea).text().trim() == 'Drop File Here'){$('#errorMultiUpload').show('slow');return false;}else{$('#errorMultiUpload').hide('slow');}
		$('.drgDesc').each(function(){
		if($(this).val().trim() == ''){jAlert('Please enter drawing title');$(this).focus();return false();}else{}
		});
/*		var returnVal = true;
		$('#innerDiv').find('div.bulkfiles').each(function(){
			var currRel = $(this).attr('rel');
			var currDivID = $(this).attr('id');
			if($(this).find('.drgTitle').val().trim() == $(this).find('.drgDesc').val().trim()){
				$(this).find('.errorDrawingTitle').show('slow');
				returnVal = false;
			}else{
				$(this).find('.errorDrawingTitle').hide('slow');
			}
		});
		console.log(returnVal);
		if(!returnVal)
			return returnVal;*/
		self._startUpload();
	}
	
	bulkUploader.prototype._preview = function(data){
		this.items = data;
		
		var FileExt = (/[.]/.exec(data[0].name)) ? /[^.]+$/.exec(data[0].name) : undefined;

		if(this.items.length > 0){
			var html = "";		
			var uId = "";
			for(var i = 0; i<this.items.length; i++){
				uId = this.items[i].name._unique();
				uId = uId.replace(/\./gi, "");
				uId = uId.replace(/'/g, "\\'");
				var sampleIcon = '<img src="images/pdf-24.png" />';
				if(FileExt == 'dwg')
					var sampleIcon = '<img src="images/dwg-24.png" />';
				var errorClass = "";
				if(typeof this.items[i] != undefined){
					if(self._validate(this.items[i].type, this.items[i].name) <= 0) {
						jAlert('Please select image file');		
						return;
						sampleIcon = '<img src="images/unknown.png" />';
						errorClass =" invalid";
					} 
					var fileTitle = this.items[i].name;
					tempArr = fileTitle.split(".");
					var lastEle = tempArr.pop();
					var processFileName = tempArr.join(".");
					var fileNameTitle = processFileName; 
					
					var revisionNo = '';
					if(processFileName.indexOf("[") !== -1){
						if(processFileName.indexOf("]") !== -1){
							tempArr = processFileName.split("[");
							var lastEle = tempArr.pop();
							var fileNameTitle = tempArr.join("[").trim();
							var revisionNoArr = lastEle.split("]");
							revisionNo = revisionNoArr[0];
						}
					}
					if(processFileName.indexOf("(") !== -1){
						if(processFileName.indexOf(")") !== -1){
							tempArr = processFileName.split("(");
							var lastEle = tempArr.pop();
							var fileNameTitle = tempArr.join("(").trim();
							var revisionNoArr = lastEle.split(")");
							revisionNo = revisionNoArr[0];
						}
					}
					if(processFileName.indexOf("{") !== -1){
						if(processFileName.indexOf("}") !== -1){
							tempArr = processFileName.split("{");
							var lastEle = tempArr.pop();
							var fileNameTitle = tempArr.join("{").trim();
							var revisionNoArr = lastEle.split("}");
							revisionNo = revisionNoArr[0];
						}
					}
					if(revisionNo == ""){
						tempArr = processFileName.split("-");
						var lastEle = tempArr.pop();
						var fileNameTitle = tempArr.join("-").trim();
						revisionNo = lastEle;
					}
					
					revisionNo = revisionNo.replace(/'/g, "\\'");
					fileNameTitle = fileNameTitle.replace(/'/g, "\\'");
					
				
					
					//jsArrtr2 = subTitleArr($('#drawingattribute1').val());
					
/*html += '<div id="selectDrawingNameHolder_'+i+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+'" rel="'+uId+'" id="ID'+uId+'"><ul id="filePanel" > <li style="margin-top:20px;width: 41%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li> <li style="margin-top:5px;" class="dataHolder"> <div id="revisionBox">Document Title <input  class="drgDesc" type="text" name="nameTitle['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="10"> <lable for="multiUpload" class="errorDrawingTitle" generated="true" class="error" style="display:none;position:absolute;margin:20px 0 0 -160px;"><div class="error-edit-profile" style="width:150px">Please Select Title.</div></lable><br/><br/> <div id="revisionBox">Description <textarea cols="10" rows="3" name="description['+fileNameTitle+revisionNo+']"></textarea> </div>  <br/><br/> <div id="revisionBox">Document Tags <textarea cols="10" rows="3" name="documentTags['+fileNameTitle+revisionNo+']" style="margin-top:5px;"></textarea> </div></li>';*/

html += '<div id="selectDrawingNameHolder_'+i+'" style="height:15px;"></div> <div class="bulkfiles'+errorClass+'" rel="'+uId+'" id="ID'+uId+'"><ul id="filePanel" > <li style="margin-top:20px;width: 41%;"> <h3 id="uploaderBulk">'+sampleIcon+'<span>'+this.items[i].name+'</span></h3> </li> <li style="margin-top:5px;" class="dataHolder"> <div id="revisionBox"><span style="vertical-align:top;"><input placeholder="Drawing Title" class="drgDesc" type="text" name="nameTitle['+fileNameTitle+revisionNo+']" value="'+fileNameTitle+'" size="20"></span>&nbsp;&nbsp;<span style="vertical-align:top;"><textarea cols="15" rows="2" name="description['+fileNameTitle+revisionNo+']" placeholder="Description"></textarea></span>&nbsp;&nbsp;<span style="vertical-align:top;"><textarea cols="15" rows="2" name="documentTags['+fileNameTitle+revisionNo+']" style="margin-top:5px;" placeholder="Tags"></textarea> </span><lable for="multiUpload" class="errorDrawingTitle" generated="true" class="error" style="display:none;position:absolute;margin:20px 0 0 -160px;"><div class="error-edit-profile" style="width:150px">Please Select Title.</div></lable></li>';
						
						html += '</ul> </div>';
				}
			}
			$("#innerDiv").append(html);
		}
	}

	bulkUploader.prototype._read = function(evt){
		if(evt.target.files){
			self._preview(evt.target.files);
			self.all.push(evt.target.files);
		} else 
			console.log("Failed file reading");
	}
	
	bulkUploader.prototype._validate = function(format, fileName){
		var FileExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		if(FileExt == 'dwg'){
			return 1;
		}else{
			var arr = this.config.support.split(",");
			return arr.indexOf(format);	
		}
	}
	
	bulkUploader.prototype._dropFiles = function(e){
		e.stopPropagation(); e.preventDefault();
		self._preview(e.dataTransfer.files);
		self.all.push(e.dataTransfer.files);
	}
//New single message start here
	var drawingDataFinal = new Array;
//New single message end here

	bulkUploader.prototype._uploader = function(file, f){
		var drawingData = new Array();
		if(typeof file[f] != undefined && self._validate(file[f].type, file[f].name) > 0){
			var requestCounter = 0;
			for(g=0; g<self.all.length; g++){
				if(self.all[g].length > 1){
					requestCounter = requestCounter + self.all[g].length;
				}else{
					requestCounter++;
				}
			}
			var data = new FormData();
			var ids = file[f].name._unique();
			data.append('file',file[f]);
			data.append('index',ids);
			
			data.append('totalRequestCount', requestCounter);
			console.log(mappingDocumentArr);
			
			data.append("mappingDocumentArr", JSON.stringify(mappingDocumentArr));//New mapping array store here
			
			$(".dfiles[rel='"+ids+"']").find(".progress").show();
			var drawingattribute2Data = '';
  			var drawingattribute2Datajs = '';
			var oldAttName = "";
			$.each($('#'+this.config.form).serializeArray(), function() {             
				data.append(this.name, this.value);
			});
			drawingData[0] = $(".bulkfiles[rel='"+ids+"']").find("h3 span").text();
			drawingData[1] = $(".bulkfiles[rel='"+ids+"']").find("h3 span").text();
			drawingData[2] = $(".bulkfiles[rel='"+ids+"']").find("div input").val();
			drawingDataFinal[f] = drawingData;
			showProgress();
			$.ajax({
				type:"POST",
				url:this.config.uploadUrl,
				data:data,
				cache: false,
				contentType: false,
				processData: false,
				success:function(rponse){
					$(".dfiles[rel='"+ids+"']").find(".progress").hide();	
					$(".dfiles[rel='"+ids+"']").find(".progress").parent().css({'border-color' : '#bcebbc', 'background-color' : '#ddffdd'});
					if (f+1 < file.length) {
						self._uploader(file,f+1);
					}else{//Send mail here
						
						if(rponse != ""){
							var jsonResult = JSON.parse(rponse);	
							if(jsonResult.status){
								hideProgress();
								setTimeout(function(){closePopup(300);}, 1000);
								RefreshTable();
							}
						}else{
							hideProgress();
							setTimeout(function(){closePopup(300);}, 1000);	
							RefreshTable();
						}
					}
				}
			});
		} else
			console.log("Invalid file format - "+file[f].name);
			
	}
	
	bulkUploader.prototype._startUpload = function(){
		if(this.all.length > 0){
			for(var k=0; k<this.all.length; k++){
				var file = this.all[k];
				this._uploader(file,0);
			}
		}
	}
	
	String.prototype._unique = function(){
		return this.replace(/[a-zA-Z]/g, function(c){
     	   return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
    	});
	}
	this._init();
}

function initBulkUploader(config){
	new bulkUploader(config);
}