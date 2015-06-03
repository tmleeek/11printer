function initPage() {
	"use strict";

	if (!window.application) {
		window.application = {};
	}

	// application initializaion starts
	var mainFuncName = 'init', subFuncArr = ['init'];
	window.application[mainFuncName] = function () {
		var i = null, j = null;
		for (i in this) {
			if (this.hasOwnProperty(i)) {
				if (i !== mainFuncName) {
					if (typeof this[i] === 'function') {
						try {
							this[i]();
						} catch (e) {
							console.log(e);
						}
					} else if (subFuncArr.length === 1 && typeof this[i][subFuncArr[0]] === 'function') {
						try {
							this[i][subFuncArr[0]]();
						} catch (e) {
							console.log(e);
						}
					} else {
						for (j = 0; j < subFuncArr.length; j = j + 1) {
							if (typeof this[i][subFuncArr[j]] === 'function') {
								try {
									this[i][subFuncArr[j]]();
								} catch (e) {
									console.log(e);
								}
							}
						}
					}
				}
			}
		}
	};

	// application initialization ends
	window.application[mainFuncName]();
}

if (document.addEventListener) {
	document.addEventListener('DOMContentLoaded', function () {
		"use strict";

		initPage();
	}, false);
} else if (document.attachEvent) {
	document.attachEvent('onreadystatechange', function () {
		"use strict";

		if (document.readyState === "complete") {
			initPage();
		
		}
	});
}


var saveYouModels = function(curcat,cname){
	console.log('saveYouModels');
	if (!!Mage.Cookies.get('ymods') && Mage.Cookies.get('ymods')!="undefined"){
		console.log('cookie not NULL');
		var nstr = Mage.Cookies.get('ymods');
		resmodels = JSON.parse(nstr);
		var arlng = Object.keys(resmodels.models).length;
		console.log(arlng);
		if(arlng > 0){
			console.log('models > 0');
			var flag = true;
			for(var i=0;i<resmodels.models.length;i++){
				if(resmodels.models[i].name === cname){
					console.log('find same model');
					flag = false;
				}
			}
			if(flag){
				console.log('add new model');
				resmodels.models.push(curcat);
				var str = JSON.stringify(resmodels);
				Mage.Cookies.set('ymods', str);
			}
		}else{
			console.log('add model to empty cookie');
				resmodels.models.push(curcat);
				var str = JSON.stringify(resmodels);
				Mage.Cookies.set('ymods', str);
		}
		
	}else{
		console.log('new cookie');
		addNewModel(curcat);
	}
}

var addNewModel = function(curcat){
	console.log('addNewModel');
	var yourmodels={"models":[
				curcat
				]};
		var str = JSON.stringify(yourmodels);
		Mage.Cookies.set('ymods', str);
}


var yourModelsView = function(){
	console.log('yourModelsView');
	if (!!Mage.Cookies.get('ymods') && Mage.Cookies.get('ymods')!="undefined"){
			var nstr = Mage.Cookies.get('ymods');
			resmodels = JSON.parse(nstr);
			var arlng = Object.keys(resmodels.models).length;
			console.log(arlng);
			if(arlng > 0){
				var cnt = resmodels.models.length;
				
				for(var i=0;i<cnt;i++){
					var mt = '<li><figure><a href="'+resmodels.models[i].href+'"><div class="img-holder"><div class="box"><img src="'+resmodels.models[i].img+'" alt="img-description" width="173" height="109"></div></div></a><figcaption><a href="#" class="title">'+resmodels.models[i].name+'</a></figcaption></figure><button class="btn-close">Закрыть</button></li>';
					jQuery('ul#resmod').append(mt);
					jQuery('#cmodls span.count i').text(cnt);
					
				}
			}
	}
}
var yourModelsViewUpdate = function(mn){
		var nstr = Mage.Cookies.get('ymods');
		resmodels = JSON.parse(nstr);
		resmodels.models = resmodels.models.filter(function( obj ) {
			return obj.name !== mn;
		});
		//for(var i=0;i<resmodels.models.length;i++){
			//if(resmodels.models[i].name === mn){
			//	console.log('++');
			//	delete resmodels.models[i]
			//}
		//} 
		var newnum = Object.keys(resmodels.models).length;
		var str = JSON.stringify(resmodels);
		Mage.Cookies.set('ymods', str);
		jQuery('#cmodls span.count i').text(newnum);
}