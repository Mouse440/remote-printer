// "USE STRICT"
//DEPRECATED
var PreviewController = (function(carouselId) {

	//Setting things up
	var carouselEle = $("#"+carouselId); //Init caurosel ele
	var leftBtn = carouselEle.children("a.left")[0]; //init left button
	var rightBtn = carouselEle.children("a.right")[0]; //init right button
	var carouselInner = $( carouselEle.children("div.carousel-inner")[0] ); //element
	var template = carouselEle.children("script#preview-images-template"); //template script element
	var previewLoaderGIF = 'images/preview-default-page.png';

	// var currentPreviewIndex = null;     			
	var previewLinks = null;					//links array
	var total = null;							//total page amount
	var reverseOrderFetchNeeded = false;		//bool for reverse order fetch
	var forwardOrderFetchNeeded = false;		//bool for forward order fetch

	/*
	* hooking up slide show
	* @event slide - update page caption
	* @event slid - fetch more links if needed and update preview links
	*/
	$("#" + carouselId)
		.on('slide.bs.carousel', updatePageCaption)
		.on('slid.bs.carousel', fetchMoreLinks);

	/*
	* Update page caption function
	* @param e - slide event
	* @return function object
	*/
	function updatePageCaption(e) {
		var currentIndex = parseInt(carouselEle.find('.active').index());
		var countingIndex = currentIndex + 1;
		var nextIndex;

		//compute next index
		if(e.direction == 'left') { //page increment, e.g. from page 4 to 5
			// nextIndex = mod(currentIndex + 1, total);
			nextIndex = mod(countingIndex, total) + 1;
		} else {					//page decrement, e.g. from page 3 to 2
			nextIndex = mod(countingIndex - 1,total) ;
			if(nextIndex == 0) { //edge case when mod yield 0
				nextIndex = total; 
			}
		}

		

			console.log("countingIndex: " + countingIndex + "nextIndex: " + nextIndex);

		//prevent movement if current page and next page is null
		if(previewLinks[countingIndex] == null && previewLinks[nextIndex] == null) {
			e.preventDefault();
			e.stopPropagation();
		} else {
			//display caption
			displayPageCaption( createPageCaption(nextIndex) );
		}
		
	}

	/*
	* Fetch more preview links from server
	* @e - slid.bs.carousel event
	*/
	function fetchMoreLinks(e) {

		//CLEAN UP START HERE - add back end throttling for previews
		//cleaup reverseOrderFetchNeeded = needReverseFetch(currentIndex);
		//forwardOrderFetchNeeded = needForwardFetch(currentIndex);
		//form validation, plot in range will change preview


		// console.log(e.direction);
		var currentIndex = parseInt(carouselEle.find('.active').index()); // get the current index
		reverseOrderFetchNeeded = needReverseFetch(currentIndex);
		forwardOrderFetchNeeded = needForwardFetch(currentIndex);

		console.log("reverse fetching needed: " + reverseOrderFetchNeeded + " forward fetching needed: " + forwardOrderFetchNeeded );
		// console.log(previewLinks[ (currentIndex - 2) % total ] + " " + previewLinks[ (currentIndex + 2) % total ]);
		// console.log( reverseIndex + " " + forwardIndex );
		
		// console.log(reverseOrderFetchNeeded);



		//check if a new query is needed
		if( forwardOrderFetchNeeded || reverseOrderFetchNeeded ) {

			$.ajax({
				data: {'reverseFetchingNeeded':reverseOrderFetchNeeded},
				url: 'php/sub_phases/fetchPreview.php',
				type: "POST",
				success: updatePreviewLinks,
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
				        alert("Error: " + errorThrown); 
				        //console.log(errorThrown);
			    },

			});
		}
			// console.log('new page is ' + projectedIndex);
		
	}

	/*
	* Update preview links when receive data from server
	* @param odata - raw data from server
	* @console - Unfatal Error: Failed to fetch preview links
	*/
	function updatePreviewLinks(odata){
		try {
			var data = JSON.parse(odata);

			console.log(data);
			var newPreLinks = (data['previewLinks'] === false) ? //checking for falsy value
								console.log("Unfatal Error: Failed to fetch preview links.")  : data['previewLinks'];

			//loop through links
			for(index in newPreLinks) {
				// console.log(index + " " + newPreLinks);
				var currentLink = newPreLinks[index];
				if( currentLink !== previewLinks[index] ){ //a new link is found!
					previewLinks[index] = currentLink;		//store new link
					var countIndex = parseInt(index);
					var previewElement = carouselInner.find('div:nth-child('+countIndex+')'); //get the nth element
					// previewElement.prop('src',currentLink);
					previewElement.children('img').prop('src',currentLink);
				}	
			}

			//reset fetch boolean
			if( data['fetchMethod'] == 'reverse' ) {
				reverseOrderFetchNeeded = false;
			} else {
				forwardOrderFetchNeeded = false;
			}
		} catch (e) {
			console.log(odata);
			console.log("Error: " + e);
			// alert("Error: " + e)
		} 
	}

	/*
	* Check if need reverse fetch 
	* @param currentIndex - current page index
	* @return bool
	*/
	function needReverseFetch(currentIndex) {
		reverseIndex = mod( (currentIndex - 2),total ) + 1;
		return previewLinks[ reverseIndex ] == null;
	}

	/*
	* Check if need forward fetch
	* @param currentIndex - current page index
	* @return bool
	*/
	function needForwardFetch(currentIndex) {
		forwardIndex = mod( (currentIndex + 2),total ) + 1;
		return previewLinks[ forwardIndex ] == null;
	}

	/*
	* Begin plopping images into slideshow and set first slide active
	*/
	function prepareSlideshow() {
		carouselInner.empty(); //flush all content

		$.each(previewLinks, function(pageNum, pLink) {
			var index = parseInt(pageNum) - 1;
			if( typeof(pLink) == 'string') {
				var item = stampPreviewEle( template,pLink ); //fetch stamped out template
				if(index == 0 ) { 					
					item.addClass('active');		//add class active to the first class
				}
				carouselInner.append( item ); 		//attach item to preview slideshow
			} else {
				var item = stampPreviewEle( template, previewLoaderGIF ); //make place holder template element
				carouselInner.append( item ); 		//attach item to preview slideshow
			}
			//display caption
			displayPageCaption( createPageCaption(1) );
		});

		/*//plot images into space
		for(index in previewLinks) {
			var link = previewLinks[index];
			if( typeof(link) == 'string' ) {
				var item = stampPreviewEle( template,link ); //fetch stamped out template
				if(index == 0 ) { 					
					item.addClass('active');		//add class active to the first class
				}
				carouselInner.append( item ); 		//attach item to preview slideshow
			} else {
				var item = stampPreviewEle( template, previewLoaderGIF ); //make place holder template element
				carouselInner.append( item ); 		//attach item to preview slideshow
			}
		}
		//display caption
		displayPageCaption( createPageCaption(0) );*/
	}

	/*
	* Display page caption
	* @param caption - caption text to display
	*/
	function displayPageCaption(caption) {
		carouselEle.find("div#preview-pages-caption").text(caption);
	}

	/*
	* Create a page caption e.g. 1 of 9
	* @nextPageIndex next page number, default is current index of active class
	*/
	function createPageCaption(nextPageIndex) {
		// currentIndex = (nextPageIndex || carouselEle.find('.active').index()) + 1;
		// currentIndex = parseInt(nextPageIndex) + 1;
		// return currentIndex + " of " + total;
		return nextPageIndex + " of " + total;
	}

	/*
	* Stamp out a preview element
	* @param JQstructure - jquery template structure
	* @param linkUrl - string url of preview image
	* @return new jquery element
	* @template				<div class="item">
						        <img class="preview-image-src" src="" alt="">
						        <div class="carousel-caption"></div>
						    </div>
	*/
	function stampPreviewEle(JQtemplate,linkUrl) {
		var newItem = $(JQtemplate.html());
		newItem.find('.preview-image-src').prop('src',linkUrl);
		return newItem;
	}

	/*
	* Get result of int n % int m, including negative mode
	* @param n - number to be modded
	* @param m - number to be modded by
	* @return result
	*/
	function mod(n,m) {
		var res = parseInt(n) % parseInt(m);
		if(n < 0) {
			res = parseInt(n) + parseInt(m) ;
		}
		return res;
	}

	return {
		/*
		* Set total 
 		* @param amount - string total document amount
		*/
		setPageTotal: function(amount) {
			total = amount;
		},

		/*
		* Mutator to set links ups
		* @param links - array of all links
		* @console - throw unfatal error
		*/
		setLinks: function(links) {
			if(links == false) {
				console.log("Unfatal Error: Preview links is empty!");
			} else {
				previewLinks = links;
			}
		},	

		/*
		* Initialize the slide show
		*/
		initSlideshow: function() {
			if(previewLinks === null || total === null) {
				throw "Please set preview links and total page amount before calling initSlideshow()."; 
			} else {
				prepareSlideshow();
			}
		},	

		/*
		* Re-initialize slide show
		*/
		reinitSlideshow: function() {

		}
	}



})("preview-carousel");