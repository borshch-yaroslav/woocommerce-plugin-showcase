<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
	class OmnisendProduct {

		/*Required*/
		public $productID;
		/*Required*/
		public $title;
		/*Required*/
		public $status;
		public $description;
		/*Required*/
		public $currency;
		/*Required*/
		public $productUrl;
		/*Required*/
		public $type;
		public $tags = [];
		public $categories = [];
		/*Required*/
		public $images = [];
		/*Required*/
		public $variants = [];

	    public static function create($id) {
	        try {
	            return new OmnisendProduct($id);
	        } catch (EmptyRequiredFieldsException $exception) {
	            return null;
	        }
	    }

		public function __construct($id){

			$wcProduct = wc_get_product( $id );
			if( empty($wcProduct) ){
				throw new EmptyRequiredFieldsException();
			}

	       	$this->productID = "" . $id;
	       	$this->title = $wcProduct->name;

		   	if( $wcProduct->stock_status == 'instock' ){
				$this->status = 'inStock';
		   	} else {
				$this->status = 'outOfStock';
		    }

	       	$this->description = $wcProduct->description;	       	
	       	$this->currency = get_woocommerce_currency();
	   		$this->productUrl = get_permalink($id);

			$this->type = 'simple';
			if( $wcProduct->is_type( 'grouped' ) ) {
				$this->type = 'grouped';
			}
			if( $wcProduct->is_type( 'variable' ) ) {
				$this->type = 'variable';
			}
			if( $wcProduct->is_type( 'external' ) ) {
				$this->type = 'external';
			}				

	        foreach( $wcProduct->tag_ids as $tag_id){
	        	$tagName = get_term( $tag_id )->name;
	        	array_push( $this->tags, $tagName );
	        }

	        foreach( $wcProduct->category_ids as $category_id){
	        	$categoryName = get_term_by( 'id', $category_id, 'product_cat' )->name;
	        	array_push( $this->categories, $categoryName );
	        }

	        $mainImage = [];
	        if( $wcProduct->image_id !== '' ){
	        	$mainImage['imageID'] = "" . $wcProduct->image_id;
	        } else {
				$mainImage['imageID'] = "" . -1;
	        }

	        $urlTmp = parse_url( wp_get_attachment_url( $wcProduct->image_id ) );
	        if( $urlTmp['path'] !== '' ){
				$mainImage['url'] = $urlTmp['scheme'] . '://' . $urlTmp['host'] . $urlTmp['path'];
	        } else {
				$mainImage['url'] = 'https://no.img';
	        }

	        $mainImage['isDefault'] = true;
	        array_push(  $this->images, $mainImage );

	        $imageCounter = 0;
	        foreach( $wcProduct->gallery_image_ids as $galImageId ){
	        	$galleryImage = [];
	        	if( $imageCounter < 9 && $galImageId != $mainImage['imageID'] ) {

	        		$galleryImage['imageID'] = "" . $galImageId;

	        		$urlTmp = parse_url( wp_get_attachment_url( $galImageId ) );
			        if( $urlTmp['path'] !== '' ){
						$galleryImage['url'] = $urlTmp['scheme'] . '://' . $urlTmp['host'] . $urlTmp['path'];
			        } else {
						$galleryImage['url'] = 'https://no.img';
			        }

			        $galleryImage['isDefault'] = false;

			        array_push( $this->images, $galleryImage );

	        		$imageCounter++;
	        	}
	        }

			if( $wcProduct->is_type( 'variable' ) ) {
				$variations = $wcProduct->get_available_variations();
			    foreach ($variations as $variation) 
			    { 
			      	$variant = [];
			      	$variantValid = true;

					/*Required field*/
			        $variant['variantID'] = "" . $variation['variation_id'];
					if( empty($variant['variantID']) ){
						$variantValid = false;
					}

					/*Required field*/
			        $variant['title'] = $variation['sku'];
					if( empty($variant['title']) ){
						$variantValid = false;
					}

			        $variant['sku'] = $variation['sku'];	

					/*Required field*/
			        if( $variation['is_in_stock'] ){
			        	$variant['status'] = 'inStock';
			        } else {
						$variant['status'] = 'outOfStock';
			       	}       
				       	
					/*Required field*/				       	
			       	$variant['price'] =  intval( $variation['display_price']  * 100 );
					if( !isset($variant['price']) ){
						$variantValid = false;
					}

			       	if( $variation['display_price'] != $variation['display_regular_price'] ){
						$variant['oldPrice'] = intval( $variation['display_regular_price']  * 100 );
			       	}

			       	$variant['productUrl'] = $this->productUrl;
			       	$variant['imageID'] = $variation['image_id'];


			       	if( $variantValid ){
			        	array_push( $this->variants, $variant );
			        }
			    }
			} else {
		        $mainVariant = [];
			    $variantValid = true;

				/*Required field*/
		        $mainVariant['variantID'] = "" . $id;
				if( empty($mainVariant['variantID']) ){
					$variantValid = false;
				}

				/*Required field*/			        
		        $mainVariant['title'] = $this->title;
				if( empty($mainVariant['title']) ){
					$variantValid = false;
				}

				/*Required field*/			        
		       	$mainVariant['status'] = $this->status;

				/*Required field*/			       	
		       	$mainVariant['price'] =  $wcProduct->get_price();
		       	if( $mainVariant['price'] === '' ){
					$mainVariant['price'] = 0;
		       	} else {
		       		$mainVariant['price'] =  intval( $mainVariant['price']  * 100 );
		       	}
				if( !isset($mainVariant['price']) ){
					$variantValid = false;
				}

		       	if( $wcProduct->is_on_sale() ) {
		       		$mainVariant['oldPrice'] = intval( $wcProduct->get_regular_price()  * 100 );
		       	}

		       	$mainVariant['productUrl'] = $this->productUrl;
		       	$mainVariant['imageID'] = $wcProduct->image_id;


		       	if( $variantValid ){
		        	array_push( $this->variants, $mainVariant );
		        }
		    }

    		if( empty($this->productID) || empty($this->title) || empty($this->status) || empty($this->currency)
    			|| empty($this->productUrl) || empty($this->type) || empty($this->images) || empty($this->variants) ){
					throw new EmptyRequiredFieldsException();
    		}
		}
	}
?>