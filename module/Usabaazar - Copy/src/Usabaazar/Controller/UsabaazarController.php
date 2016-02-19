<?php

namespace Usabaazar\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Usabaazar\Model\SearchModel;          // <-- Add this import
 use Usabaazar\Form\QueryForm;       // <-- Add this import
 use Usabaazar\Form\RegisterForm;
 use Usabaazar\Model\RegisterModel;
 

 class UsabaazarController extends AbstractActionController
 {
     
     protected $registerTable;
     
     public function indexAction()
     {
     }

     public function registerAction()
     {
         $form = new RegisterForm();
         
         $request = $this->getRequest();
         
         if($request->isPost()){
             
             $registerModel = new \Usabaazar\Model\RegisterModel();
             $form->setInputFilter($registerModel->getInputFilter());
             $form->setData($request->getPost());
             
             if($form->isValid()){
                 
                 $registerModel->exchangeArray($form->getData());
                 
                 $this->getRegisterTable()->saveRegistration($registerModel);
             }else{
                 print_r("fail");
             }
             
         }
         
         return array(
             'form' => $form
         );
     }

     public function getRegisterTable()
     {
         if(!$this->registerTable){
             $sm = $this->getServiceLocator();
             $this->registerTable = $sm->get('Usabaazar\Model\RegisterTable');
             print_r($this->registerTable);
         }
         return $this->registerTable;
     }
     
     //Amazon Material
     
     public function amazonAction()
     {  
         $form=new QueryForm();
         
         //$resultHtml=$this->setSearchParamsDisplay();
         $resultHtml="";

         $request=  $this->getRequest();
         return array(
             'form'=>$form,
             'result' => $resultHtml
         );
     }
     
     //Ebay Material
     
     public function ebayitemdisplayAction(){
         $form=new QueryForm();
         $resultHtml='';
         $itemId=-1;
         if(isset($_GET['_itemId'])){
             $itemId=$_GET['_itemId'];
         }
         $resp=$this->singleItemResponse();
         return array(
             'form'=>$form,
             'resp' => $resp,
             'itemId' => $itemId
         );
     }
     
     public function ebayAction()
     {  
         $form=new QueryForm();
         
         $resultHtml=$this->setSearchParamsDisplay();
         $request=  $this->getRequest();
         return array(
             'form'=>$form,
             'result' => $resultHtml
         );
     }
     
     
     public function ebaystoredisplayAction(){
         
         $form=new QueryForm();
         $resultHtml=$this->setStoreParamsDisplay();
         return array(
             'form'=>$form,
             'result' => $resultHtml
         );
     }
     
     
     protected function setSearchParamsDisplay(){
         $returnHtml='';
         if(isset($_GET['searchQuery'])){
             
             $searchQuery=  urldecode($_GET['searchQuery']);
             
             $pageNumber=1;
             if(isset($_GET['_pageNum'])){
                 $pageNumber=$_GET['_pageNum'];
             }
             
             $itemsPerPage=9;
             if(isset($_GET['_ipage'])){
                 $itemsPerPage=$_GET['_ipage'];
             }
             
             $categoryId=-1;
             if(isset($_GET['_cId'])){
                 $categoryId=$_GET['_cId'][0];
             }
             
             $filterByAspect=array();
             $aspectCount=0;
             foreach($_GET as $key => $value){
                 if(!((strcmp($key[0],'_')==0)||(strcmp($key, 'searchQuery')==0))){
                     $filterByAspect[$key] = $value;
                     $aspectCount=$aspectCount+1;
                 }
             }
             
             
             
             $currentURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
             
             $returnHtml=  $this->displayItems($searchQuery,$itemsPerPage,$pageNumber,$currentURL,$aspectCount,$filterByAspect,$categoryId);
         }
         return $returnHtml;
     }
     
     
     protected function setStoreParamsDisplay(){
         $returnHtml='';
         if(isset($_GET['_storeName'])){
             
             $storeName=  urldecode($_GET['_storeName']);
             
             $pageNumber=1;
             if(isset($_GET['_pageNum'])){
                 $pageNumber=$_GET['_pageNum'];
             }
             
             $itemsPerPage=9;
             if(isset($_GET['_ipage'])){
                 $itemsPerPage=$_GET['_ipage'];
             }
             
             $categoryId=-1;
             if(isset($_GET['_cId'])){
                 $categoryId=$_GET['_cId'][0];
             }
             
             $filterByAspect=array();
             $aspectCount=0;
             foreach($_GET as $key => $value){
                 if(!(strcmp($key[0],'_')==0)){
                     $filterByAspect[$key] = $value;
                     $aspectCount=$aspectCount+1;
                 }
             }
             
             $currentURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
             
             $returnHtml=  $this->displayStoreItems($storeName,$itemsPerPage,$pageNumber,$currentURL,$aspectCount,$filterByAspect,$categoryId);
         }
         return $returnHtml;
     }
     
     
     
     protected function displayItems($searchQuery,$itemsPerPage,$pageNumber,$cURL,$aspectCount,$filterByAspect,$categoryId)
                {
         
         $safeQuery = $searchQuery;
         $itemsPerPage=$itemsPerPage;
         $pageNumber=$pageNumber;
         $currentURL=$cURL;
        $filterByAspectLength=$aspectCount;


        $aspectFilterString=''; 
        $aspectPropertyCounter=0;
        $aspectPropertyParentString='';

        if($filterByAspectLength>0){
            $filterByAspect=$filterByAspect;
        foreach($filterByAspect as $key=>$value){
            $newkey=  str_replace("_", " ", $key);
            $aspectFilterString .="&aspectFilter($aspectPropertyCounter).aspectName=".  $newkey;
            $aspectFilterString .="&aspectFilter($aspectPropertyCounter).aspectValueName=$value";
            $aspectPropertyCounter++;
            }
            
        }
        
        

        $categoryId=$categoryId;
        $categoryFilterString='';
        if($categoryId!=-1){
            $categoryFilterString .="&categoryId=$categoryId";
        }


        $returnData=array();


        error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging



  
        $results = '';
        $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
        $version = '1.0.0';  // API version supported by your application
        $appid = 'TamalRoy-9a55-471a-be23-e0cff4a5acb4';  // Replace with your own AppID
        $globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)

  
        $responseEncoding = 'XML';   // Format of the response

        //$safeQuery = urlencode (($_POST['searchQuery']));
        $site  = 'EBAY-US';
  
  
  
  
                $apicall = "$endpoint?OPERATION-NAME=findItemsAdvanced"
                       . "&SERVICE-VERSION=1.0.0"
                       . "&GLOBAL-ID=$site"
                       . "&SECURITY-APPNAME=TamalRoy-9a55-471a-be23-e0cff4a5acb4" //replace with your app id
                       . "&keywords=$safeQuery"
                       . "&paginationInput.entriesPerPage=$itemsPerPage"
                       . "&paginationInput.pageNumber=$pageNumber"
                       . "&outputSelector(0)=CategoryHistogram"
                       . "&outputSelector(1)=AspectHistogram"
                       . "&outputSelector(2)=GalleryInfo"
                       . "&sortOrder=BestMatch"
                       . "&itemFilter(0).name=ListingType"
                       . "&itemFilter(0).value=FixedPrice"
                       . "&affiliate.networkId=9" 
                       . "&affiliate.trackingId=1234567890"
                       . "&affiliate.customId=456"
                       . "&RESPONSE-DATA-FORMAT=$responseEncoding"
                       . "$aspectFilterString"
                       . "$categoryFilterString";

                
               
 
                    $resp = simplexml_load_file($apicall);
                    if($resp == null){
                        
                    }
                    
//                    $s_endpoint = 'http://open.api.ebay.com/shopping';
//
//                $s_version = '667';   // Shopping API version number
//                $f_version = '1.4.0';   // Finding API version number
//                $appID   = 'TamalRoy-9a55-471a-be23-e0cff4a5acb4'; //replace this with your AppID
//                $globalID    = 'EBAY-US';
//
//                  $sitearray = array(
//                    'EBAY-US' => '0',
//                    'EBAY-ENCA' => '2',
//                    'EBAY-GB' => '3',
//                    'EBAY-AU' => '15',
//                    'EBAY-DE' => '77',
//                  );
//  
//            $siteID = $sitearray[$globalID];
// 
//            $itemApicall = "$s_endpoint?callname=GetSingleItem"
//                  . "&version=$s_version"
//                  . "&siteid=$siteID"
//                  . "&appid=$appID"
//                  . "&IncludeSelector=Details,FeedbackHistory,Variations"   
//                  . "&responseencoding=$responseEncoding"
//                  . "&ItemID=";
//    
    
    
    
                    $pTitle=array();

                    if ($resp->ack == "Success" && $resp->paginationOutput->totalEntries > 0) {

                    $count=0;

                    for($count=0;$count<($itemsPerPage/3) && $count<(($resp->paginationOutput->totalEntries)/3);$count++){
                    //$resp->searchResult->item[$count*3]->galleryInfoContainer->galleryURL[0]
                //        simplexml_load_file($itemApicall.$resp->searchResult->item[$count*3]->itemId)->Item->PictureURL
                    $results .='<div class="box1">';
                    if($resp->searchResult->item[$count*3]){
                    $results .=		   '<div class="col_1_of_single1 span_1_of_single1"><a href="ebayitemdisplay?_itemId='.$resp->searchResult->item[$count*3]->itemId.'">
                                                     <div class="view1 view-fifth1">
                                                          <div class="top_box">
                                                                <h3 class="m_1">'.$resp->searchResult->item[$count*3]->title.'</h3>

                                                         <div class="grid_img">
                                                                   <div class="css3"><img src="'.$resp->searchResult->item[$count*3]->galleryURL.'" alt=""/></div>
                                                                  <div class="mask1">
                                                        <div class="info">Quick View</div>
                                                          </div>
                                            </div>
                                       <div class="price">'.$resp->searchResult->item[$count*3]->sellingStatus->convertedCurrentPrice.$resp->searchResult->item[$count*3]->sellingStatus->convertedCurrentPrice['currencyId'].'</div>
                                                           </div>
                                                            </div>
                                                           <span class="rating1">
                                                        <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1">
                                                        <label for="rating-input-1-5" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1">
                                                        <label for="rating-input-1-4" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1">
                                                        <label for="rating-input-1-3" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1">
                                                        <label for="rating-input-1-2" class="rating-star"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1">
                                                        <label for="rating-input-1-1" class="rating-star"></label>&nbsp;
                                                  (45)
                                              </span>
                                                                 <ul class="list2">
                                                                  <li>
                                                                        <img src="/images/plus.png" alt=""/>
                                                                        <ul class="icon1 sub-icon1 profile_img">
                                                                          <li><a class="active-icon c1" href="#">Add To Bag </a>
                                                                                <ul class="sub-icon1 list">
                                                                                        <li><h3>sed diam nonummy</h3><a href=""></a></li>
                                                                                        <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                                                                                </ul>
                                                                          </li>
                                                                         </ul>
                                                                   </li>
                                                             </ul>
                                                    <div class="clear"></div>
                                                </a></div>';
                    }
                    if($resp->searchResult->item[$count*3+1]){
                                $results .='<div class="col_1_of_single1 span_1_of_single1"><a href="ebayitemdisplay?_itemId='.$resp->searchResult->item[$count*3+1]->itemId.'">
                                                     <div class="view1 view-fifth1">
                                                          <div class="top_box">
                                                                <h3 class="m_1">'.$resp->searchResult->item[$count*3+1]->title.'</h3>

                                                         <div class="grid_img">
                                                                   <div class="css3"><img src="'.$resp->searchResult->item[$count*3+1]->galleryInfoContainer->galleryURL[0].'" alt=""/></div>
                                                                  <div class="mask1">
                                                        <div class="info">Quick View</div>
                                                          </div>
                                            </div>
                                       <div class="price">'.$resp->searchResult->item[$count*3+1]->sellingStatus->convertedCurrentPrice.$resp->searchResult->item[$count*3+1]->sellingStatus->convertedCurrentPrice['currencyId'].'</div>
                                                           </div>
                                                            </div>
                                                           <span class="rating1">
                                                        <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1">
                                                        <label for="rating-input-1-5" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1">
                                                        <label for="rating-input-1-4" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1">
                                                        <label for="rating-input-1-3" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1">
                                                        <label for="rating-input-1-2" class="rating-star"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1">
                                                        <label for="rating-input-1-1" class="rating-star"></label>&nbsp;
                                                  (45)
                                              </span>
                                                                 <ul class="list2">
                                                                  <li>
                                                                        <img src="/images/plus.png" alt=""/>
                                                                        <ul class="icon1 sub-icon1 profile_img">
                                                                          <li><a class="active-icon c1" href="#">Add To Bag </a>
                                                                                <ul class="sub-icon1 list">
                                                                                        <li><h3>sed diam nonummy</h3><a href=""></a></li>
                                                                                        <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                                                                                </ul>
                                                                          </li>
                                                                         </ul>
                                                                   </li>
                                                             </ul>
                                                    <div class="clear"></div>
                                                </a></div>';
                    }
                       if($resp->searchResult->item[$count*3+2]){
                                $results .='<div class="col_1_of_single1 span_1_of_single1"><a href="ebayitemdisplay?_itemId='.$resp->searchResult->item[$count*3+2]->itemId.'">
                                                     <div class="view1 view-fifth1">
                                                          <div class="top_box">
                                                                <h3 class="m_1">'.$resp->searchResult->item[$count*3+2]->title.'</h3>

                                                         <div class="grid_img">
                                                                   <div class="css3"><img src="'.$resp->searchResult->item[$count*3+2]->galleryInfoContainer->galleryURL[0].'" alt=""/></div>
                                                                  <div class="mask1">
                                                        <div class="info">Quick View</div>
                                                          </div>
                                            </div>
                                       <div class="price">'.$resp->searchResult->item[$count*3+2]->sellingStatus->convertedCurrentPrice.$resp->searchResult->item[$count*3+2]->sellingStatus->convertedCurrentPrice['currencyId'].'</div>
                                                           </div>
                                                            </div>
                                                           <span class="rating1">
                                                        <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1">
                                                        <label for="rating-input-1-5" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1">
                                                        <label for="rating-input-1-4" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1">
                                                        <label for="rating-input-1-3" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1">
                                                        <label for="rating-input-1-2" class="rating-star"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1">
                                                        <label for="rating-input-1-1" class="rating-star"></label>&nbsp;
                                                  (45)
                                              </span>
                                                                 <ul class="list2">
                                                                  <li>
                                                                        <img src="/images/plus.png" alt=""/>
                                                                        <ul class="icon1 sub-icon1 profile_img">
                                                                          <li><a class="active-icon c1" href="#">Add To Bag </a>
                                                                                <ul class="sub-icon1 list">
                                                                                        <li><h3>sed diam nonummy</h3><a href=""></a></li>
                                                                                        <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                                                                                </ul>
                                                                          </li>
                                                                         </ul>
                                                                   </li>
                                                             </ul>
                                                    <div class="clear"></div>
                                                </a></div>';
                       }
                                        $results .= '<div class="clear"></div>
                                          </div>';
                        }
                    }else{
                        $results .='Not Found';
                    }


                    $itemDisplayHtml='<div class="cont span_2_of_3">

                                  <div class="mens-toolbar">
                              <div class="sort">
                                <div class="sort-by">
                                            <label>Sort By</label>
                                            <select>
                                                            <option value="">
                                                    Popularity               </option>
                                                            <option value="">
                                                    Price : High to Low               </option>
                                                            <option value="">
                                                    Price : Low to High               </option>
                                            </select>
                                            <a href=""><img src="/images/arrow2.gif" alt="" class="v-middle"></a>
                               </div>
                                </div>
                                  <div class="pager">   
                                   <div class="limiter visible-desktop">
                                    <label>Show</label>
                                    <select id="itemsPerPageOption" onchange="itemsPerPageFunction();" >';

                            if($itemsPerPage==9){
                            $itemDisplayHtml.='<option value="9" selected="selected">
                                            9                </option>
                                                    <option value="15">
                                            15                </option>
                                                    <option value="30">
                                            30                </option>
                                                </select> per page';        
                            }else if($itemsPerPage==15){
                             $itemDisplayHtml.='<option value="9">
                                            9                </option>
                                                    <option value="15" selected="selected">
                                            15                </option>
                                                    <option value="30">
                                            30                </option>
                                                </select> per page';   
                            }else{
                              $itemDisplayHtml.='<option value="9">
                                            9                </option>
                                                    <option value="15">
                                            15                </option>
                                                    <option value="30" selected="selected">
                                            30                </option>
                                                </select> per page';  
                            }
                            $itemDisplayHtml.='</div>

                                                <div class="clear"></div>
                                </div>
                            <div class="clear"></div>
                               </div>'.$results;

                    $query=  parse_url($currentURL, PHP_URL_QUERY);

                function addToURL( $key, $value, $url) {
                    $info = parse_url( $url );
                    parse_str( $info['query'], $query );
                    return $info['scheme'] . '://' . $info['host'] . $info['path'] . '?' . http_build_query( $query ? array_merge( $query, array($key => $value ) ) : array( $key => $value ) );
                }


                    $displayPageNumber ='<div class="mens-toolbar"><div class="pager"><ul class="dc_pagination dc_paginationA dc_paginationA06"><li><a href="#" class="previous">Pages</a></li>';

                                $totalPages=$resp->paginationOutput->totalPages;
                                $pageNumberCount=1;
                                $pageNumberCounter;
                                if($pageNumber<10 && $totalPages>=20){
                                    $pageNumberCounter=1;
                                    while($pageNumberCounter <= $pageNumber){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a class="pageNumber" href="'.  addToURL($key, $value, $url).'" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                    while($pageNumberCount<=20){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a class="pageNumber" href="'.  addToURL($key, $value, $url).'" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                }else{
                                    $pageNumberCounter=$pageNumber-10;
                                    $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                    while($pageNumberCounter <= $pageNumber){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a href="'.  addToURL($key, $value, $url).'" class="pageNumber" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                    while($pageNumberCount<=20){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a href="'.  addToURL($key, $value, $url).'" class="pageNumber" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                }
                                 $displayPageNumber .='</ul>
                                                <div class="clear"></div>
                                </div>
                            <div class="clear"></div>
                               </div></div>';

                    $itemDisplayHtml .=$displayPageNumber;

                    $aspectResult='';
                    $aspectAttribute=array();


                    if($resp->ack == "Success"){

                        if($resp->aspectHistogramContainer->aspect){

                        foreach($resp->aspectHistogramContainer->aspect as $aspect){
                            $aspectResult .='<section  class="sky-form">
                                                        <h4>';


                            foreach($aspect->attributes() as $aname=>$aspectName){
                                //print_r($aspectName);
                                $aspectResult.=$aspectName;
                                array_push($aspectAttribute, $aspectName);					
                            }


                            $aspectResult.='</h4>
                                                <div class="row row1 scroll-pane">
                                                <div class="col col-4">';
                            foreach($aspect->valueHistogram as $valueHistogram){
                                $aspectResult.='<label class="checkbox"><input type="checkbox" class="aspectTag" name="'.$aspect->attributes().'" value="'.$valueHistogram->attributes().'" onclick="go();"><i></i>';
                                foreach($valueHistogram->attributes() as $vName=>$valueName){
                                    //print_r($valueName);
                                    $aspectResult.=$valueName;
                                }
                                $aspectResult.='('.$valueHistogram->count.')</label>';
                            }
                            $aspectResult.='</div>
                                                                </div>
                                       </section>';


                    }
                        }


                    }else{
                        $aspectResult.='No Aspect';
                    }
                    $aspectResult .= $aspectPropertyParentString;



                    $categoryResult='';

                    $categoryCount=0;

                    if($resp){
                        if($resp->categoryHistogramContainer->categoryHistogram){
                            $categoryResult .='<section  class="sky-form" style="padding-bottom: 25px">
                                                        <h4 style="font-size:1 em; color: #222; font-weight: 600;">Categories</h4>
                                                        <div class="row row1 scroll-pane" style="padding:5px; max-height: 300px;">
                                                        <div class="col col-4">';
                        foreach($resp->categoryHistogramContainer->categoryHistogram as $categoryHist){
                            $key='_cId';$value=$categoryHist->categoryId;$url=$currentURL;
                            $categoryResult .='<label class="checkbox" style="padding-left: 0px; font-size: 13px"><a class="categoryForm" href="'.  addToURL($key,$value, $url).'">'.$categoryHist->categoryName.'</a>('.$categoryHist->count.')</label>';            
                            if($categoryCount===0){
                            foreach($categoryHist->childCategoryHistogram as $childHistogram){
                                $key='_cId';$value=$childHistogram->categoryId;$url=$currentURL;
                                $categoryResult .='<label class="checkbox"><a href="'.  addToURL($key,$value, $url).'">'.$childHistogram->categoryName.'</a>('.$childHistogram->count.')</label>';
                            }

                            }
                            $categoryCount++;

                        }
                        $categoryResult.='</div></div></section>';
                    }

                }

                $aspectResultHtml='<div class="rsidebar span_1_of_left">'.$categoryResult.$aspectResult.'</div>';    
                $returnHtml='<div class="wrap">'.$aspectResultHtml.$itemDisplayHtml;
                $returnHtml.='<div class="clear"></div>
                                        </div>
                                   </div>';


            return $returnHtml;
     }
     
     
     protected function singleItemResponse(){
         
         $resp=null;
         
        if(isset($_GET['_itemId'])){
            $itemId=$_GET['_itemId'];

            error_reporting(E_ALL);

            $s_endpoint = 'http://open.api.ebay.com/shopping';
            $responseEncoding = 'XML';   // Format of the response
            $s_version = '667';   // Shopping API version number
            $f_version = '1.4.0';   // Finding API version number
            $appID   = 'TamalRoy-9a55-471a-be23-e0cff4a5acb4'; //replace this with your AppID
            $globalID    = 'EBAY-US';

              $sitearray = array(
                'EBAY-US' => '0',
                'EBAY-ENCA' => '2',
                'EBAY-GB' => '3',
                'EBAY-AU' => '15',
                'EBAY-DE' => '77',
              );

             $siteID = $sitearray[$globalID];

             $apicall = "$s_endpoint?callname=GetSingleItem"
                   . "&version=$s_version"
                   . "&siteid=$siteID"
                   . "&appid=$appID"
                   . "&ItemID=$itemId"
                   . "&IncludeSelector=Details,FeedbackHistory,Variations,ShippingCosts"   
                   . "&responseencoding=$responseEncoding";

             //$apicall = "http://open.api.ebay.com/shopping?callname=GetSingleItem &version=667 &siteid=0 &appid=TamalRoy-9a55-471a-be23-e0cff4a5acb4 &ItemID=$itemId &IncludeSelector=Details,FeedbackHistory,Variations &responseencoding=XML";

             $resp = simplexml_load_file($apicall);
        }
        
        return $resp;
     }
     
     
     protected function displayStoreItems($_storeName,$itemsPerPage,$pageNumber,$cURL,$aspectCount,$filterByAspect,$categoryId)
             {
         
         $storeName = $_storeName;
         $itemsPerPage=$itemsPerPage;
         $pageNumber=$pageNumber;
         $currentURL=$cURL;
        $filterByAspectLength=$aspectCount;


        $aspectFilterString=''; 
        $aspectPropertyCounter=0;
        $aspectPropertyParentString='';

        if($filterByAspectLength>0){
            $filterByAspect=$filterByAspect;
        foreach($filterByAspect as $key=>$value){
            $aspectFilterString .="&aspectFilter($aspectPropertyCounter).aspectName=$key";
            $aspectFilterString .="&aspectFilter($aspectPropertyCounter).aspectValueName=$value";
            $aspectPropertyCounter++;
            }
        }

        $categoryId=$categoryId;
        $categoryFilterString='';
        if($categoryId!=-1){
            $categoryFilterString .="&categoryId=$categoryId";
        }


        $returnData=array();


        error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging



  
        $results = '';
        $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
        $version = '1.0.0';  // API version supported by your application
        $appid = 'TamalRoy-9a55-471a-be23-e0cff4a5acb4';  // Replace with your own AppID
        $globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)

  
        $responseEncoding = 'XML';   // Format of the response

        $site  = 'EBAY-US';
  
  
  
  
                $apicall = "$endpoint?OPERATION-NAME=findItemsIneBayStores"
                            . "&SERVICE-VERSION=1.0.0"
                            . "&GLOBAL-ID=$site"
                            . "&SECURITY-APPNAME=TamalRoy-9a55-471a-be23-e0cff4a5acb4" //replace with your app id
                            . "&storeName=$storeName"
                            . "&paginationInput.entriesPerPage=$itemsPerPage"
                            . "&paginationInput.pageNumber=$pageNumber"
                            . "&outputSelector(0)=CategoryHistogram"
                            . "&outputSelector(1)=AspectHistogram"
                            . "&outputSelector(2)=GalleryInfo"
                            . "&sortOrder=BestMatch"
                            . "&affiliate.networkId=9" 
                            . "&affiliate.trackingId=1234567890"
                            . "&affiliate.customId=456"
                            . "&RESPONSE-DATA-FORMAT=$responseEncoding"
                            . "$aspectFilterString"
                            . "$categoryFilterString";


  
                    $resp = simplexml_load_file($apicall);


                    $s_endpoint = 'http://open.api.ebay.com/shopping';

                $s_version = '667';   // Shopping API version number
                $f_version = '1.4.0';   // Finding API version number
                $appID   = 'TamalRoy-9a55-471a-be23-e0cff4a5acb4'; //replace this with your AppID
                $globalID    = 'EBAY-US';

                  $sitearray = array(
                    'EBAY-US' => '0',
                    'EBAY-ENCA' => '2',
                    'EBAY-GB' => '3',
                    'EBAY-AU' => '15',
                    'EBAY-DE' => '77',
                  );
  
            $siteID = $sitearray[$globalID];
 
            $itemApicall = "$s_endpoint?callname=GetSingleItem"
                  . "&version=$s_version"
                  . "&siteid=$siteID"
                  . "&appid=$appID"
                  . "&IncludeSelector=Details,FeedbackHistory,Variations"   
                  . "&responseencoding=$responseEncoding"
                  . "&ItemID=";
    
    
    
    
                    $pTitle=array();

                    if ($resp->ack == "Success" && $resp->paginationOutput->totalEntries > 0) {

                    $count=0;

                    for($count=0;$count<($itemsPerPage/3) && $count<(($resp->paginationOutput->totalEntries)/3);$count++){
                    //$resp->searchResult->item[$count*3]->galleryInfoContainer->galleryURL[0]
                //        simplexml_load_file($itemApicall.$resp->searchResult->item[$count*3]->itemId)->Item->PictureURL
                    $results .='<div class="box1">';
                    if($resp->searchResult->item[$count*3]){
                    $results .=		   '<div class="col_1_of_single1 span_1_of_single1"><a href="ebayitemdisplay?_itemId='.$resp->searchResult->item[$count*3]->itemId.'">
                                                     <div class="view1 view-fifth1">
                                                          <div class="top_box">
                                                                <h3 class="m_1">'.$resp->searchResult->item[$count*3]->title.'</h3>

                                                         <div class="grid_img">
                                                                   <div class="css3"><img src="'.$resp->searchResult->item[$count*3]->galleryURL.'" alt=""/></div>
                                                                  <div class="mask1">
                                                        <div class="info">Quick View</div>
                                                          </div>
                                            </div>
                                       <div class="price">'.$resp->searchResult->item[$count*3]->sellingStatus->convertedCurrentPrice.$resp->searchResult->item[$count*3]->sellingStatus->convertedCurrentPrice['currencyId'].'</div>
                                                           </div>
                                                            </div>
                                                           <span class="rating1">
                                                        <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1">
                                                        <label for="rating-input-1-5" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1">
                                                        <label for="rating-input-1-4" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1">
                                                        <label for="rating-input-1-3" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1">
                                                        <label for="rating-input-1-2" class="rating-star"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1">
                                                        <label for="rating-input-1-1" class="rating-star"></label>&nbsp;
                                                  (45)
                                              </span>
                                                                 <ul class="list2">
                                                                  <li>
                                                                        <img src="/images/plus.png" alt=""/>
                                                                        <ul class="icon1 sub-icon1 profile_img">
                                                                          <li><a class="active-icon c1" href="#">Add To Bag </a>
                                                                                <ul class="sub-icon1 list">
                                                                                        <li><h3>sed diam nonummy</h3><a href=""></a></li>
                                                                                        <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                                                                                </ul>
                                                                          </li>
                                                                         </ul>
                                                                   </li>
                                                             </ul>
                                                    <div class="clear"></div>
                                                </a></div>';
                    }
                    if($resp->searchResult->item[$count*3+1]){
                                $results .='<div class="col_1_of_single1 span_1_of_single1"><a href="ebayitemdisplay?_itemId='.$resp->searchResult->item[$count*3+1]->itemId.'">
                                                     <div class="view1 view-fifth1">
                                                          <div class="top_box">
                                                                <h3 class="m_1">'.$resp->searchResult->item[$count*3+1]->title.'</h3>

                                                         <div class="grid_img">
                                                                   <div class="css3"><img src="'.$resp->searchResult->item[$count*3+1]->galleryInfoContainer->galleryURL[0].'" alt=""/></div>
                                                                  <div class="mask1">
                                                        <div class="info">Quick View</div>
                                                          </div>
                                            </div>
                                       <div class="price">'.$resp->searchResult->item[$count*3+1]->sellingStatus->convertedCurrentPrice.$resp->searchResult->item[$count*3+1]->sellingStatus->convertedCurrentPrice['currencyId'].'</div>
                                                           </div>
                                                            </div>
                                                           <span class="rating1">
                                                        <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1">
                                                        <label for="rating-input-1-5" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1">
                                                        <label for="rating-input-1-4" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1">
                                                        <label for="rating-input-1-3" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1">
                                                        <label for="rating-input-1-2" class="rating-star"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1">
                                                        <label for="rating-input-1-1" class="rating-star"></label>&nbsp;
                                                  (45)
                                              </span>
                                                                 <ul class="list2">
                                                                  <li>
                                                                        <img src="/images/plus.png" alt=""/>
                                                                        <ul class="icon1 sub-icon1 profile_img">
                                                                          <li><a class="active-icon c1" href="#">Add To Bag </a>
                                                                                <ul class="sub-icon1 list">
                                                                                        <li><h3>sed diam nonummy</h3><a href=""></a></li>
                                                                                        <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                                                                                </ul>
                                                                          </li>
                                                                         </ul>
                                                                   </li>
                                                             </ul>
                                                    <div class="clear"></div>
                                                </a></div>';
                    }
                       if($resp->searchResult->item[$count*3+2]){
                                $results .='<div class="col_1_of_single1 span_1_of_single1"><a href="ebayitemdisplay?_itemId='.$resp->searchResult->item[$count*3+2]->itemId.'">
                                                     <div class="view1 view-fifth1">
                                                          <div class="top_box">
                                                                <h3 class="m_1">'.$resp->searchResult->item[$count*3+2]->title.'</h3>

                                                         <div class="grid_img">
                                                                   <div class="css3"><img src="'.$resp->searchResult->item[$count*3+2]->galleryInfoContainer->galleryURL[0].'" alt=""/></div>
                                                                  <div class="mask1">
                                                        <div class="info">Quick View</div>
                                                          </div>
                                            </div>
                                       <div class="price">'.$resp->searchResult->item[$count*3+2]->sellingStatus->convertedCurrentPrice.$resp->searchResult->item[$count*3+2]->sellingStatus->convertedCurrentPrice['currencyId'].'</div>
                                                           </div>
                                                            </div>
                                                           <span class="rating1">
                                                        <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1">
                                                        <label for="rating-input-1-5" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1">
                                                        <label for="rating-input-1-4" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1">
                                                        <label for="rating-input-1-3" class="rating-star1"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1">
                                                        <label for="rating-input-1-2" class="rating-star"></label>
                                                        <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1">
                                                        <label for="rating-input-1-1" class="rating-star"></label>&nbsp;
                                                  (45)
                                              </span>
                                                                 <ul class="list2">
                                                                  <li>
                                                                        <img src="/images/plus.png" alt=""/>
                                                                        <ul class="icon1 sub-icon1 profile_img">
                                                                          <li><a class="active-icon c1" href="#">Add To Bag </a>
                                                                                <ul class="sub-icon1 list">
                                                                                        <li><h3>sed diam nonummy</h3><a href=""></a></li>
                                                                                        <li><p>Lorem ipsum dolor sit amet, consectetuer  <a href="">adipiscing elit, sed diam</a></p></li>
                                                                                </ul>
                                                                          </li>
                                                                         </ul>
                                                                   </li>
                                                             </ul>
                                                    <div class="clear"></div>
                                                </a></div>';
                       }
                                        $results .= '<div class="clear"></div>
                                          </div>';
                        }
                    }else{
                        $results .='Not Found';
                    }


                    $itemDisplayHtml='<div class="cont span_2_of_3">

                                  <div class="mens-toolbar">
                              <div class="sort">
                                <div class="sort-by">
                                            <label>Sort By</label>
                                            <select>
                                                            <option value="">
                                                    Popularity               </option>
                                                            <option value="">
                                                    Price : High to Low               </option>
                                                            <option value="">
                                                    Price : Low to High               </option>
                                            </select>
                                            <a href=""><img src="/images/arrow2.gif" alt="" class="v-middle"></a>
                               </div>
                                </div>
                                  <div class="pager">   
                                   <div class="limiter visible-desktop">
                                    <label>Show</label>
                                    <select id="itemsPerPageOption" onchange="itemsPerPageFunction();" >';

                            if($itemsPerPage==9){
                            $itemDisplayHtml.='<option value="9" selected="selected">
                                            9                </option>
                                                    <option value="15">
                                            15                </option>
                                                    <option value="30">
                                            30                </option>
                                                </select> per page';        
                            }else if($itemsPerPage==15){
                             $itemDisplayHtml.='<option value="9">
                                            9                </option>
                                                    <option value="15" selected="selected">
                                            15                </option>
                                                    <option value="30">
                                            30                </option>
                                                </select> per page';   
                            }else{
                              $itemDisplayHtml.='<option value="9">
                                            9                </option>
                                                    <option value="15">
                                            15                </option>
                                                    <option value="30" selected="selected">
                                            30                </option>
                                                </select> per page';  
                            }
                            $itemDisplayHtml.='</div>

                                                <div class="clear"></div>
                                </div>
                            <div class="clear"></div>
                               </div>'.$results;

                    $query=  parse_url($currentURL, PHP_URL_QUERY);

                function addToURL( $key, $value, $url) {
                    $info = parse_url( $url );
                    parse_str( $info['query'], $query );
                    return $info['scheme'] . '://' . $info['host'] . $info['path'] . '?' . http_build_query( $query ? array_merge( $query, array($key => $value ) ) : array( $key => $value ) );
                }


                    $displayPageNumber ='<div class="mens-toolbar"><div class="pager"><ul class="dc_pagination dc_paginationA dc_paginationA06"><li><a href="#" class="previous">Pages</a></li>';

                                $totalPages=$resp->paginationOutput->totalPages;
                                $pageNumberCount=1;
                                $pageNumberCounter;
                                if($pageNumber<10 && $totalPages>=20){
                                    $pageNumberCounter=1;
                                    while($pageNumberCounter <= $pageNumber){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a class="pageNumber" href="'.  addToURL($key, $value, $url).'" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                    while($pageNumberCount<=20){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a class="pageNumber" href="'.  addToURL($key, $value, $url).'" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                }else{
                                    $pageNumberCounter=$pageNumber-10;
                                    $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                    while($pageNumberCounter <= $pageNumber){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a href="'.  addToURL($key, $value, $url).'" class="pageNumber" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                    while($pageNumberCount<=20){
                                        $key='_pageNum';$value=$pageNumberCounter;$url=$currentURL;
                                        $displayPageNumber .='<li><a href="'.  addToURL($key, $value, $url).'" class="pageNumber" value="'.$pageNumberCounter.'">'.$pageNumberCounter.'</a></li>';
                                        $pageNumberCount++;
                                        $pageNumberCounter++;
                                    }
                                }
                                 $displayPageNumber .='</ul>
                                                <div class="clear"></div>
                                </div>
                            <div class="clear"></div>
                               </div></div>';

                    $itemDisplayHtml .=$displayPageNumber;

                    $aspectResult='';
                    $aspectAttribute=array();


                    if($resp->ack == "Success"){

                        if($resp->aspectHistogramContainer->aspect){

                        foreach($resp->aspectHistogramContainer->aspect as $aspect){
                            $aspectResult .='<section  class="sky-form">
                                                        <h4>';


                            foreach($aspect->attributes() as $aname=>$aspectName){
                                //print_r($aspectName);
                                $aspectResult.=$aspectName;
                                array_push($aspectAttribute, $aspectName);					
                            }


                            $aspectResult.='</h4>
                                                <div class="row row1 scroll-pane">
                                                <div class="col col-4">';
                            foreach($aspect->valueHistogram as $valueHistogram){
                                $aspectResult.='<label class="checkbox"><input type="checkbox" class="aspectTag" name="'.$aspect->attributes().'" value="'.$valueHistogram->attributes().'" onclick="go();"><i></i>';
                                foreach($valueHistogram->attributes() as $vName=>$valueName){
                                    //print_r($valueName);
                                    $aspectResult.=$valueName;
                                }
                                $aspectResult.='('.$valueHistogram->count.')</label>';
                            }
                            $aspectResult.='</div>
                                                                </div>
                                       </section>';


                    }
                        }


                    }else{
                        $aspectResult.='No Aspect';
                    }
                    $aspectResult .= $aspectPropertyParentString;



                    $categoryResult='';

                    $categoryCount=0;

                    if($resp){
                        if($resp->categoryHistogramContainer->categoryHistogram){
                            $categoryResult .='<section  class="sky-form" style="padding-bottom: 25px">
                                                        <h4 style="font-size:1 em; color: #222; font-weight: 600;">Categories</h4>
                                                        <div class="row row1 scroll-pane" style="padding:5px; max-height: 300px;">
                                                        <div class="col col-4">';
                        foreach($resp->categoryHistogramContainer->categoryHistogram as $categoryHist){
                            $key='_cId';$value=$categoryHist->categoryId;$url=$currentURL;
                            $categoryResult .='<label class="checkbox" style="padding-left: 0px; font-size: 13px"><a class="categoryForm" href="'.  addToURL($key,$value, $url).'">'.$categoryHist->categoryName.'</a>('.$categoryHist->count.')</label>';            
                            if($categoryCount===0){
                            foreach($categoryHist->childCategoryHistogram as $childHistogram){
                                $key='_cId';$value=$childHistogram->categoryId;$url=$currentURL;
                                $categoryResult .='<label class="checkbox"><a href="'.  addToURL($key,$value, $url).'">'.$childHistogram->categoryName.'</a>('.$childHistogram->count.')</label>';
                            }

                            }
                            $categoryCount++;

                        }
                        $categoryResult.='</div></div></section>';
                    }

                }

                $aspectResultHtml='<div class="rsidebar span_1_of_left">'.$categoryResult.$aspectResult.'</div>';    
                $returnHtml='<div class="wrap">'.$aspectResultHtml.$itemDisplayHtml;
                $returnHtml.='<div class="clear"></div>
                                        </div>
                                   </div>';


            return $returnHtml;
     }
     
     
 }