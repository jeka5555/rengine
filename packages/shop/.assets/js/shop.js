$(function() {

	// Cart
	// ----
	$('.widget-cart').each(function() {

		var cart = this;

		Global.addListener('shoppingCartUpdated', function(e) {
			
			if (e.data == null) e.data = {};
			
			$(cart).find('.total').html(safeAssign(e.data.total, 0));
			$(cart).find('.count').html(safeAssign(e.data.count, 0));
	
		});
	});

	// Order function
	// --------------
	shopOrder = function() {
		API.action({
			'action' : '/module/shop/order'
		});
	}

});


// Cart list
CartList = function(args) {

	// This
	// ----
	var cartList = this;

	// Widget
	// ------
	this.widget = $(args.widget);
	this.bindWidget();

	// Listen
	// -------
	Global.addListener('shoppingCartUpdated', function(e) {
		cartList.update();
	});

};

CartList.prototype = $.extend({}, {


	// Bind widget's features
	// ----------------------
	bindWidget : function() {


		var cartList = this;

		// Clear button
		// ---------------
		$(this.widget).find('.clear-cart').click(function() {
				// clear
				// ------
				API.action({
					'action' : '/module/shop/clearCart'
				});
		});

		// Continue button
		// ---------------
		$(this.widget).find('.continue a').click(function() {
			$(this).parents('.ui-dialog').remove();
		});

		// Order button
		// ------------
		$(this.widget).find('.order-button').click(function() {
			cartList.order();
		});

		// Products
		// --------
		$(this.widget).find('.widget-node').each(function() {

			var productID = $(this).data('node-id');

			// Bind delete button
			// ------------------
			$(this).find('.remove-from-cart').click(function() {

				var data = {};
				data[productID] = 0;

				// Update
				// ------
				API.action({
					'action' : '/module/shop/addToCart',
					'data' : data
				});

			});

			bindWidget();

		});
	},

	// Update content
	// --------------
	update : function() {


		var cartList = this;

		API.action({
			'action' : '/module/shop/updateCartList',
			'callback' : function(result) {

				if (result == null) return;
				$(cartList.widget).empty();
				$(cartList.widget).replaceWith(result.widget);

			}
		});
	},

	// Order
	// -----
	order : function() {

		// This
		// ----
		var cartList = this;
		
		API.action({
			'action' : '/module/widgets/get',
			'data' : ['order'],
			'callback' : function(result) {

				var form = $(result);
				var popupOrder = new Popup({
					'dialogClass' : 'site-popup',
					'title' : 'Оформление заказа',
					'width' : 550,
					'content' : form,
					'modal' : true,
					'collapsable' : false,
					'resizable' : false,
					'minimizable' : false,
				});			
			}
		})
	}
});
