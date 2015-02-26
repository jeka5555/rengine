// Components list
// ---------------
Components = {

	get : function(type, id) {

		// if type and id is defined
		// -------------------------
		if (id != null) {

			// Chech for component data
			// ------------------------
			if (! ComponentTypes.hasOwnProperty(type) ) return null;
			if (! ComponentTypes[type].hasOwnProperty(id)) return null;

			// Return component class
			// ----------------------
			return ComponentTypes[type][id];
		}
	},

	// Get all by type
	// ---------------
	getByType : function(type) {
		return ComponentTypes[type];
	}

};

ComponentTypes = {};

// Basic core component
// --------------------
Component = function(args) {};

// Simple component
// ----------------
Component.prototype = $.extend({}, Events.prototype, {

	// Get component instance
	// ----------------------
	getInstance : function(args) {
		return this(args);
	}

});

// Create new component class
// --------------------------
Component.register = function(args) {

	// Component class
	// ---------------
	var component = {};

	// Add constructor
	// ---------------
	component.constructor =  function() {
		Component.constructor.call(component);
	};

	// Add options
	// -----------
	component.component = args;

	// Add override constructor
	// ------------------------
	if (args.constructor != null) {
		component.constructor = args.constructor;
	}

	// Append prototype
	// ----------------
	component.prototype = $.extend({}, Component.prototype, args);

	// Process inheritance
	// -------------------
	if (args.inherit != null) {

		$.each(args.inherit, function(parentIndex, parentData) {

			// Get parent
			// ----------
			var parentComponent = Components.get(parentData[0], parentData[1]);
			if (parentComponent == null) return;

			// Mix prototypes
			// --------------
			var oldProto = component.prototype;
			var newProto = parentComponent.prototype;
			var proto = $.extend({},newProto, oldProto);

			// Add constructor
			// ---------------
			proto.parentConstructor = parentComponent.constructor;

			// Parent prototupe
			// ----------------
			proto.parent = parentComponent.prototype;

			// Merge into one
			// --------------
			component.prototype = $.extend({}, proto);



		})
	}

	// Add prototype
	// -------------
	component.constructor.prototype = component.prototype;
	component.type = args.type;
	component.id = args.id;
	component.title = args.title;

	// Create constructor method
	// -------------------------
	component.getInstance = function(args) {
		return new component.constructor(args);
	};


	// Type
	// ----
	if (args.type != null) {
		if (! ComponentTypes.hasOwnProperty(args.type)) ComponentTypes[args.type] = {};
		ComponentTypes[args.type][args.id] = component;
	}

	// Return completed component class
	// --------------------------------
	return component;

}
