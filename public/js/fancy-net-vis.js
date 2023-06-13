// this is the source code for a flexible, general visualzation tool

function add_node_selection_listener(ctx,callback){
    ctx.node_selection_listeners.push(callback)
}

function add_mouse_listener_to_canvas(canvas,ctx){

    $(".hovermenu").detach().appendTo(document.documentElement);

    is_mouse_down = false
    is_mouse_dragging = false  
    held_node = null
    held_pos = null

    canvas.onmousemove = function(e) {
        ctx.show_legend = false

        
        // get mouse position in terms of coordinates
        // that were used for drawing on the canvas
        var rect = this.getBoundingClientRect(),
        x = e.clientX - rect.left,
        y = e.clientY - rect.top;

        // handle click-and-drag
        if( is_mouse_down ){
            is_mouse_dragging = true
            if( held_node != null ){
                held_node.x = x/ctx.view_scale - ctx.view_offset[0]
                held_node.y = y/ctx.view_scale - ctx.view_offset[1]
                ctx.node_coords[held_node.data.gene_id] = [held_node.x,held_node.y]
                update_edges(ctx)
            }
            if( held_pos != null ){
                ctx.view_offset[0] = (x-held_pos[0])/ctx.view_scale;
                ctx.view_offset[1] = (y-held_pos[1])/ctx.view_scale;
            }
        } else {
            // show legend if hovering over help icon
            if( is_mouse_on_button(x,y,ctx.help_icon) ){
                ctx.show_legend = true
            }
        }
        

        // make hover caption invisible by default
        var hovermenu = $('.hovermenu');
        hovermenu.removeClass('visible');
        canvas.style.cursor = 'default'
        
        // check for buttons at mouse position
        for( var i = 0 ; i < ctx.standard_buttons.length ; i++ ){
            var btn = ctx.standard_buttons[i]
            if( btn.visible && is_mouse_on_button(x,y,btn) ){
                btn.hl = true
                canvas.style.cursor = 'pointer'
            } else {
                btn.hl = false
            }
        }
        
        // check for nodes at mouse position
        // starting with the "top" (most visible) nodes
        ctx.hover_node = get_node_at_mouse_pos( ctx, x, y )
                
        if( ctx.hover_node != null ){
            // got a hit, show caption  and stop checking
            hovermenu.addClass('visible');
            hovermenu.css({top: e.pageY+10, left: e.pageX+10});
            var selected = (ctx.hover_node == ctx.selected_node)
            hovermenu.html( get_hover_html_for_node(ctx.hover_node.data, selected) )
            if( !selected ){
                canvas.style.cursor = 'pointer'
            }
            update_display( ctx )
            return
        }
        
        // check for edges near mouse position
        x = x/ctx.view_scale - ctx.view_offset[0]
        y = y/ctx.view_scale - ctx.view_offset[1]
        i = ctx.edges.length-1;
        while(edge = ctx.edges[i--]) {
            
            // find distance^2 from mouse to edge
            dp = [x-edge.a[0], y-edge.a[1]]
            a = (dp[0]*edge.d[0] + dp[1]*edge.d[1])/edge.det
            if( (a<0) || (a>1) ){
                continue   
            }
            np = [edge.a[0] + a*edge.d[0], edge.a[1] + a*edge.d[1]]
            d2 = Math.pow(np[0]-x,2) + Math.pow(np[1]-y,2)
            
            if( d2 < 100 ){
                
                // got a hit, show caption  and stop checking
                hovermenu.addClass('visible');
                hovermenu.css({top: e.pageY+10, left: e.pageX+10});
                hovermenu.html( get_details_for_edge(edge.data) )

                update_display( ctx )
                return
            }
        }

        update_display( ctx )
    };

    canvas.onmouseout = function(e){
        $('.hovermenu').removeClass('visible')
        for( var i = 0 ; i < ctx.standard_buttons.length ; i++ ){
            ctx.standard_buttons[i].hl = false
        }
        update_display( ctx )
    }
    
    canvas.addEventListener('mousedown', function(e) {
        is_mouse_down = true

        var rect = this.getBoundingClientRect(),
        x = e.clientX - rect.left,
        y = e.clientY - rect.top;
        
        held_node = get_node_at_mouse_pos( ctx, x, y )
        if(held_node != null ){
            held_node.held = true
            held_pos = null
        } else {
            held_pos = [
                x-(ctx.view_offset[0]*ctx.view_scale),
                y-(ctx.view_offset[1]*ctx.view_scale)
            ]
        }

        update_display( ctx )

    })
    
    canvas.addEventListener('mouseup', function(e) {

        i = ctx.nodes.length-1;
        while(node = ctx.nodes[i--]) {
            node.held = false
        }

        var was_mouse_dragging = is_mouse_dragging
        is_mouse_down = false
        is_mouse_dragging = false
        held_pos = null
        held_node = null
        
        if( (!was_mouse_dragging) ){
            //click on button
            if( ctx.load_more_button.hl && ctx.selected_node ){
                load_more_data_from_api(ctx) 
                return
            } else if ( ctx.link_button.hl && ctx.selected_node ){
                var url = get_url_for_node( ctx.selected_node.data )
                var win = window.open(url, '_blank');
                if (win) {
                    win.focus();
                }
                return
            }
        
            // click on node (or deselect node)
            ctx.selected_node = ctx.hover_node 
            for( var i = 0 ; i < ctx.node_selection_listeners.length ; i++ ){
                ctx.node_selection_listeners[i](ctx.selected_node)   
            }
            ctx.api_url = get_api_url(ctx)   
            update_display(ctx)  
            if( ctx.hover_node != null ){
                var selected = (ctx.hover_node == ctx.selected_node)
                $('.hovermenu').html( get_hover_html_for_node(ctx.hover_node.data, selected) )
            }
        }
    })
    
    canvas.onwheel = function(event){
        // adjust scale, then adjust offset to maintain center
        var old_scale = ctx.view_scale
        ctx.view_scale -= event.deltaY/1000
        var m = ctx.view_scale / old_scale
        var vw = ctx.canvasWidth / m
        var vh = ctx.canvasHeight / m 
        ctx.view_offset[0] += (vw-ctx.canvasWidth)/2
        ctx.view_offset[1] += (vh-ctx.canvasHeight)/2
        update_display( ctx )
        event.preventDefault();
    };
}

function get_api_url_suffix(ctx){
    if( ctx.selected_node == null ){
        return '';
    }
    return "8,ASC," + ctx.selected_node.data.gene_id + ",,,"
}

function get_api_url(ctx){
    if( ctx.selected_node == null ){
        return ctx.default_api_url   
    } else {
    var parts = ctx.default_api_url.split('/')
    var base_url = parts.slice(0,-1).join('/')
        return base_url + '/' + get_api_url_suffix(ctx);
    }   
}

function get_hover_html_for_node(node_data, selected){
    if( selected ){
        return node_data.gene_id + "<br>Details are Shown";
    } else {
        return node_data.gene_id + "<br>Click to Show Details"; 
    }  
    
}

// button is a rectnagle with properties x,y,w,h
function is_mouse_on_button( x, y, button ){
    return button && (x>button.x) && (x<(button.x+button.w)) && (y>button.y) && (y<(button.y+button.h))
}
    
function get_node_at_mouse_pos( ctx, x, y ){

        
    // check for nodes at mouse position
    // starting with the "top" (most visible) nodes
    i = ctx.nodes.length-1;
    while(node = ctx.nodes[i--]) {
        build_path_for_node_on_canvas( ctx, node.x, node.y, node.data, ctx.view_scale, ctx.view_offset )
        if( ctx.isPointInPath(x, y) ){
            return node
        }
    }

    return null
}

function get_details_for_edge( all_edge_data ){
    var content;
    if( all_edge_data.length < 1 ){
        return "no interaction";
    } else if(all_edge_data.length == 1){
        content = "<b>Interaction</b>";
    } else {
        content = '<b>' + all_edge_data.length + ' Interactions</b>';
    }
    for (var i = 0; i < all_edge_data.length; i++) {
        var edge_data = all_edge_data[i];
        var clist = ['','']
        if( edge_data.distance != 'NA' ){
            clist.push('Distance to annotated<br>peak in TSS: ' + edge_data.distance + ' kb');
        }
        if( edge_data.experiment != 'NA' ){
            clist.push('Experiment: '  + edge_data.experiment);
        }
        if( edge_data.type != 'NA' ){
            clist.push('Type: '  + edge_data.type);
        }
        if( edge_data.conf != 'NA' ){
            clist.push('Confirmation: '  + edge_data.conf);
        }
        clist.push('Regulator: ' + edge_data.gene_id);
        clist.push('Target: ' + edge_data.target_id);
        clist.push('Edge ID: ' + edge_data.edge_id);
            
        content += clist.join('<br>')
    }
   return content;
}


function update_display( ctx ){
    update_buttons( ctx )
    
    // clear display
    ctx.clearRect(0, 0, ctx.canvasWidth, ctx.canvasHeight);  
    
    // draw edges
    n_edges = ctx.edges.length
    for( var i = 0 ; i < n_edges ; i++ ){
        drawEdge(ctx, ctx.edges[i], ctx.view_scale, ctx.view_offset)
    }

    // draw nodes
    ctx.strokeStyle = 'black'
    ctx.font = '12px monospaced';
    ctx.lineWidth = 1;
    ctx.setLineDash([]);
    n_nodes = ctx.nodes.length
    for( var i = 0 ; i < n_nodes ; i++ ){
        var node = ctx.nodes[i]
        drawNode(ctx, node, ctx.view_scale, ctx.view_offset )
    }
    for( var i = 0 ; i < n_nodes ; i++ ){
        var node = ctx.nodes[i]
        drawNodeLabel(ctx, node, ctx.view_scale, ctx.view_offset )
    }

    if( ctx.selected_node != null ){
        drawNodeMenu(ctx)   
    }
    
    if( ctx.show_legend ){
        drawLegend( ctx )
    } else {
        drawHelpIcon( ctx ) 
    }    
    
    drawStandardButtons(ctx)
    
    //draw outer border around entire canvas
    drawRect( ctx, 1,1,ctx.canvasWidth-2,ctx.canvasHeight-2, fillStyle=null, strokeStyle='black' )
}

function load_more_data_from_api(ctx){
    
    if( ctx.selected_node == null ){
        return   
    }
    
    var gid = ctx.selected_node.data.gene_id
    if(!(gid in ctx.draw_num_by_gid)){
        ctx.draw_num_by_gid[gid] = -1   
    }
    ctx.draw_num_by_gid[gid] += 1
    
    // request json data from api
    $.ajax({
        
        url: ctx.api_url + "/" + ctx.draw_num_by_gid[gid], 
        
        // update network when data is received
        success: function(response_data){
            json_data = JSON.parse(response_data)
            update_network_with_json(ctx,json_data)
            for( var i = 0 ; i < ctx.node_selection_listeners.length ; i++ ){
                ctx.node_selection_listeners[i](ctx.selected_node)   
            }
        }
        
    })
}

function update_network_with_json( ctx, json_data ){
    var new_nodes = json_data['nodes']
    var new_edges = json_data['edges']
    
    
    // identify which nodes are actually new
    var actually_new_nodes = []
    var n = new_nodes.length
    for( var i = 0 ; i < n ; i++ ){
        var existing_node = get_node( ctx, new_nodes[i].gene_id )
        if( existing_node == null ) {
            actually_new_nodes.push(new_nodes[i])
        }
    }    
    
    // insert new nodes into visualization
    var n = actually_new_nodes.length
    for( var i = 0 ; i < n ; i++ ){
        var node_data = actually_new_nodes[i]
        var p = pick_new_node_location( ctx, node_data, new_edges)
        ctx.nodes.push({
            x: p[0],
            y: p[1],
            data: node_data
        })
        ctx.node_coords[node_data.gene_id] = p
    }
    
    // insert new edge data into visualization
    var n = new_edges.length
    for( var i = 0 ; i < n ; i++ ){
        var edge_id = new_edges[i].edge_id
        
        if( ctx.all_single_edge_ids.includes(edge_id) ){
            continue   
        }        
        
        var udp_hash = new_edges[i].udp_hash
        if( udp_hash in ctx.edges_by_udp_hash ){
            
            // add support to existing edge
            var existing_edge = ctx.edges_by_udp_hash[udp_hash];
            existing_edge.data.push(new_edges[i]);
            
        } else {
        
            // add new edge
            var gene_id = new_edges[i].gene_id
            var target_id = new_edges[i].target_id
            var new_edge = {
                data: [new_edges[i]],
                gene_id: gene_id,
                target_id: target_id
            }
            ctx.edges.push(new_edge)
            ctx.edges_by_udp_hash[udp_hash] = new_edge
        }
        
        ctx.all_single_edge_ids.push(edge_id)
    }
    update_pdi_counts(ctx)
    update_edges(ctx)
    update_display(ctx)
}

// for each node build a list of related interactions shown
// (used to compute the number of interactions not being shown)
function update_pdi_counts(ctx){
    var node_data_by_gene_id = {}
    for( var i = 0 ; i < ctx.nodes.length ; i++ ){
        var data = ctx.nodes[i].data
        data.hidden_pdi_count = data.pdi_count
        data.related_pdis = []
        node_data_by_gene_id[data.gene_id] = data
    }
    
    for( var i = 0 ; i < ctx.edges.length ; i++ ){
        var e = ctx.edges[i]
        var tf_data = node_data_by_gene_id[e.gene_id]
        var tar_data = node_data_by_gene_id[e.target_id]
        
        tf_data.hidden_pdi_count -= e.data.length
        tf_data.related_pdis = tf_data.related_pdis.concat(e.data)
        
        tar_data.hidden_pdi_count -= e.data.length
        tar_data.related_pdis = tar_data.related_pdis.concat(e.data)
    }
}

// pick a good location for a new node
// used in update_network_with_json
function pick_new_node_location( ctx, new_node_data, new_edges ){
    if( ctx.nodes.length == 0 ){
        return [0,0]   
    }
    
    var gid = new_node_data.gene_id
    
    // check for existing neighbor
    var n = new_edges.length
    for( var i = 0; i < n ; i++ ) {
        var ne = new_edges[i]
        var neighbor = null
        if( ne.gene_id == gid ){
            neighbor = get_node( ctx, ne.target_id )
        } else if( ne.target_id == gid ) {
            neighbor = get_node( ctx, ne.gene_id )
        }
        if(neighbor){
            return pick_neighbor_location( ctx, neighbor )
        }
    }
    
    // no existing neighbors, so pick an arbitrary open spot
    return pick_neighbor_location( ctx, ctx.nodes[0] )
}

// pick an open location nearby an existing node
// used in pick_new_node_location()
function pick_neighbor_location( ctx, existing_node ){
    
    // spiral outward until an open location is found
    var a = 0
    var r = 100
    var maxr = 100000
    var dr = 50
    while( r < maxr ) {
        var tx = existing_node.x + r*Math.cos(a)
        var ty = existing_node.y + r*Math.sin(a)
        if( is_location_open(ctx,tx,ty) ){
            return [tx,ty] // found a good spot
        }
        if( a > Math.PI*2 ){
            a = 0
            r += dr
        } else {
            a += dr/r
        }
    }
    
    // give up and return some location
    return [200,200]
}

// return true if there is space for a new node centered at the given location
// used in pick_neighbor_location
function is_location_open( ctx, x, y ){
    var maxr2 = 100*100
    var n = ctx.nodes.length
    for( var i = 0; i < n ; i++ ) {
        var node = ctx.nodes[i]
        var r2 = Math.pow(x-node.x,2) + Math.pow(y-node.y,2)
        if( r2 < maxr2 ){
            return false;   
        }
    }
    return true
}

// get an existing node by identifier
function get_node( ctx, gene_id ){
    var n = ctx.nodes.length
    for( var i = 0 ; i < n_nodes ; i++ ){
        var node = ctx.nodes[i]
        if( node.data.gene_id == gene_id ) {
            return node   
        }
    }    
    return null
}


function show_network_with_static_json( ctx, w, h, json_data, init_protein_name=null ){

    ctx.view_offset = [0,0]
    ctx.view_scale = 1
    ctx.canvasWidth = w
    ctx.canvasHeight = h
    
    var nodes = json_data['nodes']
    var edges = json_data['edges']
    
    var n_nodes = nodes.length
    var da = Math.PI*2 / n_nodes
    var dist = 200
    
    var node_coords = {}
    var node_data_by_gid = {}
    for( var i = 0 ; i < n_nodes ; i++ ){        
        var x = w/2 + dist*Math.cos(i*da);
        var y = h/2 + dist*Math.sin(i*da);
        var gid = nodes[i].gene_id
        
        // if an initial protein name as provided,
        // assume it's first batch of interaction data has already been loaded
        if( isTF(nodes[i]) && (nodes[i].protein_name==init_protein_name) ){
            ctx.draw_num_by_gid[gid] = 0
        }
        
        node_coords[gid] = [x,y]
        node_data_by_gid[gid] = nodes[i]
    }
    
    // prepare to build edges
    // - udp_hash (undirected pair hash) relates parellel edges
    // - edge_id is distinct for each individual edge
    ctx.edges = [] // list of vis 'edges' (groups of parellel edges with matching udp_hash)
    ctx.edges_by_udp_hash = {} // keys are udp_hashes, values are elements of ctx.edges
    ctx.all_single_edge_ids = [] // list of all individual edge IDs in vis
    
    
    
    // build edges
    for( var i = 0 ; i < edges.length ; i++ ){
        var edge_id = edges[i].edge_id
        if( edge_id in ctx.all_single_edge_ids ){
            continue   
        }
        
        var udp_hash = edges[i].udp_hash
        if( udp_hash in ctx.edges_by_udp_hash ){
            
            // add support to existing edge
            var existing_edge = ctx.edges_by_udp_hash[udp_hash];
            existing_edge.data.push(edges[i]);
            
        } else {
        
            // add new edge
            var gene_id = edges[i].gene_id
            var target_id = edges[i].target_id
            var a = node_coords[gene_id]
            var b = node_coords[target_id]
            var d = [b[0]-a[0],b[1]-a[1]]
            var new_edge = {
                a: a,
                b: b,
                d: d,
                det: d[0]*d[0] + d[1]*d[1],
                data: [edges[i]],
                gene_id: gene_id,
                target_id: target_id
            }
            ctx.edges.push(new_edge)
            ctx.edges_by_udp_hash[udp_hash] = new_edge
        }
        
        ctx.all_single_edge_ids.push( edge_id )
    }
    
    // build nodes
    ctx.node_coords = node_coords
    ctx.nodes = []
    for( var i = 0 ; i < n_nodes ; i++ ){
        var label = nodes[i].gene_id;
        var xy = node_coords[label]
    
        ctx.nodes[i] = {
            x: xy[0],
            y: xy[1],
            data: nodes[i]
        }
    }    

    // init variables for user interaction
    ctx.hover_node = null
    ctx.selected_node = null
    ctx.node_selection_listeners = []
    ctx.help_icon = {x:2,y:2,w:20,h:20}
    ctx.load_more_button = {x:(w-285),y:170,w:148,h:20,hl:false,
                            text:'Load More Interactions'}
    ctx.link_button = {x:(w-285),y:28,w:148,h:20,hl:false,
                            text:'Link'}
    ctx.deselect_button = {x:(w-20),y:1,w:20,h:20,hl:false,text:'x'}
    
    // list of "typical" buttons
    // (when hovered, highlight button and change mouse pointer)
    ctx.standard_buttons = [
        ctx.load_more_button,
        ctx.link_button,
        ctx.deselect_button,
    ]
    
    update_pdi_counts(ctx)
    update_display( ctx )
}

// show/hide buttons depending on the situation
function update_buttons(ctx){
    if( ctx.selected_node == null ){
        ctx.load_more_button.visible = false
        ctx.deselect_button.visible = false
        ctx.link_button.visible = false
    } else {
        ctx.link_button.visible = true
        ctx.link_button.text = get_node_link_text(ctx.selected_node.data)
        
        ctx.deselect_button.visible = true
        
        var hpc = ctx.selected_node.data.hidden_pdi_count
        ctx.load_more_button.visible = (hpc > 0)
    }
    
    ctx.help_icon.visible = true
}

// update edge coordinates to match nodes
function update_edges(ctx){
    
    for( var i = 0 ; i < ctx.edges.length ; i++ ){
        var edge = ctx.edges[i]
        var a = ctx.node_coords[edge.gene_id]
        var b = ctx.node_coords[edge.target_id]
        var d = [b[0]-a[0],b[1]-a[1]]
        edge.a = a
        edge.b = b
        edge.d = d
        edge.det = d[0]*d[0] + d[1]*d[1]
    }
    
}


function show_network_with_api( ctx, w, h, api_url, success_func=null, init_tfname=null ){
    
    ctx.default_api_url = api_url
    ctx.api_url = api_url
    ctx.draw_num_by_gid = {}
    
    // request json data from api
    $.ajax({
        
        url: api_url + "/0", 
        
        // show network when data is received
        success: function(response_data){
            json_data = JSON.parse(response_data)
            show_network_with_static_json( ctx, w, h, json_data, init_tfname )
            if( success_func != null ){
                success_func(response_data);   
            }
        }
        
    })
}


function show_network_with_callback( ctx, w, h, callback_function ) {
       
    // get json data from the callback function
    json_data = callback_function()
    show_network_with_static_json( ctx, w, h, json_data )
}


function drawEdge( ctx, edge, scale=1, offset=[0,0] ){
    
    var a = edge.a
    var b = edge.b
    var support = edge.data.length
    
    // find the set of disctinct colors which should be represented
    var all_colors = []
    for( var i = 0 ; i < support ; i++ ){
        var color = edge.data[i].color
        if( !all_colors.includes(color) ){
            all_colors.push(color)   
        }
    }
    
    drawSingleEdge(ctx, a[0],a[1],b[0],b[1], all_colors, scale, offset)
}


function drawSingleEdge(context, fromx, fromy, tox, toy, all_colors, scale=1, offset=[0,0]) {
    context.strokeStyle = all_colors[0];      
    context.lineCap = "round";
    context.lineWidth = 2;
    
    var fromx = (fromx+offset[0])*scale
    var fromy = (fromy+offset[1])*scale
    var tox = (tox+offset[0])*scale
    var toy = (toy+offset[1])*scale
    var dx = tox-fromx
    var dy = toy-fromy
    
    // draw arrowhead in the middle of the edge
    var r = .5
    var ax = fromx + dx*r
    var ay = fromy + dy*r
    drawArrow(context, fromx, fromy, ax, ay)
    
    // draw multicolored segments
    var n = all_colors.length
    var seg_dx = dx/n
    var seg_dy = dy/n
    for( var i = 0 ; i < n ; i++ ){
        context.strokeStyle = all_colors[i];  
        context.beginPath();
        context.moveTo( fromx + seg_dx*i, fromy + seg_dy*i );
        context.lineTo( fromx + seg_dx*(i+1), fromy + seg_dy*(i+1) );
        context.stroke();
    }
}

function drawArrow(context, fromx, fromy, tox, toy) {
    var headlen = 10;
    var dx = tox - fromx;
    var dy = toy - fromy;
    var angle = Math.atan2(dy, dx);
    
    context.beginPath();
    context.moveTo(fromx, fromy);
    context.lineTo(tox, toy);
    context.lineTo(tox - headlen * Math.cos(angle - Math.PI / 6), toy - headlen * Math.sin(angle - Math.PI / 6));
    context.moveTo(tox, toy);
    context.lineTo(tox - headlen * Math.cos(angle + Math.PI / 6), toy - headlen * Math.sin(angle + Math.PI / 6));
    context.stroke();
}


function build_path_for_node_on_canvas( ctx, x, y, node_data, scale=1, offset=[0,0] ){
    
    var x = (x+offset[0]) * scale
    var y = (y+offset[1]) * scale
    var radius = 45*scale
    
    ctx.beginPath()
    ctx.arc(x, y, radius, 0, 2 * Math.PI, false)
}

function isTF( node_data ){
    try{
        return Object.hasOwn(node_data, 'protein_name')
    }catch(error){
        return node_data.hasOwnProperty('protein_name')
    }
}

function drawStandardButtons(ctx){
    
    for( var i = 0 ; i < ctx.standard_buttons.length ; i++ ){
        var btn = ctx.standard_buttons[i]
        if(!btn.visible){
            continue   
        }
        drawRect( ctx, btn.x, btn.y, btn.w, btn.h, (btn.hl?'black':'#EEE'), '#000' )

        ctx.fillStyle = (btn.hl?'white':'black')
        ctx.textAlign = 'center';
        ctx.fillText(btn.text, btn.x+btn.w/2, btn.y+btn.h/2+3);
    }
}

function drawHelpIcon(ctx){
    x = ctx.help_icon.x
    y = ctx.help_icon.y
    w = ctx.help_icon.w
    h = ctx.help_icon.h
    drawRect( ctx, x, y, w, h, '#EEE', '#000' )
    
    ctx.fillStyle = 'black'
    ctx.textAlign = "center";
    ctx.fillText('?', 12, 16 );
}



function pathRect( ctx,x,y,w,h){
    ctx.beginPath()
    ctx.moveTo(x,y)
    ctx.lineTo(x+w,y)
    ctx.lineTo(x+w,y+h)
    ctx.lineTo(x,y+h)
    ctx.lineTo(x,y)
}


function drawRect( ctx, x,y,w,h, fillStyle=null, strokeStyle=null ){
    if( fillStyle ){
        ctx.fillStyle = fillStyle
        pathRect( ctx,x,y,w,h)
        ctx.fill()
    }
    if( strokeStyle ){
        ctx.lineWidth = 1
        ctx.strokeStyle = strokeStyle
        pathRect( ctx,x,y,w,h)
        ctx.stroke()
    }
}

function drawNodeMenu(ctx){
    if( ctx.selected_node == null ){
        return   
    }
    
    var x = ctx.canvasWidth-300
    var y = 1
    var w = 299
    var h = 200
    drawRect( ctx, x, y, w, h, '#EEE', '#000' )

    // draw small version of the selected node
    var scale = .5
    var thumbnail_node = {
        x: (x+230)/scale,
        y: (y+50)/scale,
        data: ctx.selected_node.data
    };
    drawNode( ctx, thumbnail_node, .5)
    drawNodeLabel( ctx, thumbnail_node, .5)
    
    // draw details text
    draw_node_details( ctx, x, y, ctx.selected_node.data )
}

    
function draw_node_details( ctx,x,y, node_data ){
    ctx.fillStyle = 'black'
    ctx.textAlign = 'left'
    ctx.font = '12px Arial'

    var ox = 10
    var oy = 20 
    var dy = 20

    ctx.fillText('Selected Node Info', x+ox, y+oy);
    oy += 2.5*dy
    
    if( isTF(node_data) ){
        ctx.fillText('Type: Transcription Factor', x+ox, y+oy);
        oy += dy
        ctx.fillText('TF Name: ' + node_data.protein_name, x+ox, y+oy);
        oy += dy
        ctx.fillText('Gene ID: ' + node_data.gene_id, x+ox, y+oy);
    } else {
        ctx.fillText('Type: Gene', x+ox, y+oy);
        oy += dy
        ctx.fillText('Gene ID: ' + node_data.gene_id, x+ox, y+oy);
    }
    
    oy = 160
    
    var hpc = node_data.hidden_pdi_count
    if( hpc > 0 ){
        var hidden_report = "(" + hpc.toLocaleString("en-US") + " interactions not loaded)"
    } else {
        var pc = node_data.pdi_count
        var hidden_report = "(showing all " + pc.toLocaleString("en-US") + " interactions)"
    }
    ctx.fillText(hidden_report, x+ox, y+oy);
}

function drawLegend(ctx){

    var x = 2
    var y = 2
    var w = 250
    var h = 350
    drawRect( ctx, x, y, w, h, '#EEE', '#000' )

    ctx.textAlign = 'left';
    ctx.font = '12px Arial'
    ctx.fillStyle = 'black'
    ctx.fillText('Legend:', x+35, y+40);

    var legend_nodes = [
        {
            x: x+260,
            y: y+90,
            data: {
                protein_name: 'TF'
            }
        },
        {
            x: x+380,
            y: y+90,
            data: {
                gene_id: 'Gene'
            }
        },
    ];
    drawNode( ctx, legend_nodes[0], .5)
    drawNode( ctx, legend_nodes[1], .5)
    drawNodeLabel( ctx, legend_nodes[0], .5 )
    drawNodeLabel( ctx, legend_nodes[1], .5 )
    
    // database-specific function
    draw_edge_legend( ctx, x+30, y+110 )
    
    var yo = 240
    var xo = 30
    var dy = 20
    ctx.textAlign = "left";
    ctx.fillStyle = 'black'
    var text_lines = [
        'Hover over nodes or edges for info',
        'Click nodes to show details',
        'Click and drag nodes to move them',
        'Click and drag background to pan',
        'Zoom with the mousewheel',
    ]
    for( var i = 0 ; i<text_lines.length ; i++ ){
        ctx.fillText(text_lines[i], x+xo, y+yo);
        yo += dy
    }
    ctx.textAlign = "center";
    
    
}

function drawNode(ctx, node, scale=1, offset=[0,0] ) {
    
    var x = node.x
    var y = node.y
    var node_data = node.data

    var strokeWidth = 1
    var stroke = 'black'
    if( node == ctx.selected_node ){
        // draw large black shadow to indicate that this node is selected
        var gradient = ctx.createRadialGradient(
            (x+offset[0]) * scale, (y+offset[1]) * scale, scale*50, 
            (x+offset[0]) * scale, (y+offset[1]) * scale, scale*70);
        gradient.addColorStop(0, 'black');
        gradient.addColorStop(1, "rgba(0,0,0,0)");
        ctx.fillStyle = gradient;
        ctx.beginPath()
        ctx.arc(
            (x+offset[0]) * scale, 
            (y+offset[1]) * scale, 
            70*scale, 0, 2 * Math.PI, false)
        ctx.fill();
    }
    
    var fill = '#AFA'
    if( isTF(node_data) ){
        fill = '#FAA'
    }
    
    // draw shape
    ctx.fillStyle = fill
    ctx.lineWidth = strokeWidth
    ctx.strokeStyle = stroke
    build_path_for_node_on_canvas( ctx, x, y, node_data, scale, offset )
    if (fill) {
        ctx.fill()
        
        // draw marble effect
        var gradient = ctx.createRadialGradient(
            (x+offset[0]) * scale, (y+offset[1]) * scale, scale*40, 
            (x+offset[0]) * scale, (y+offset[1]) * scale, scale*50);
        gradient.addColorStop(0, "rgba(0,0,0,0)");
        gradient.addColorStop(1, 'black');
        ctx.fillStyle = gradient;
        build_path_for_node_on_canvas( ctx, x, y, node_data, scale, offset )
        ctx.fill();
        
        var gradient = ctx.createLinearGradient(
            (x+offset[0]) * scale, (y+offset[1]-30) * scale, 
            (x+offset[0]) * scale, (y+offset[1]+30) * scale);
        gradient.addColorStop(0, "rgba(255,255,255,.6)");
        gradient.addColorStop(1, 'rgba(0,0,0,0)');
        ctx.fillStyle = gradient;
        build_path_for_node_on_canvas( ctx, x, y, node_data, scale, offset )
        ctx.fill();
    }
    if (stroke) {
        ctx.stroke()
    }
}


function drawNodeLabel(ctx, node, scale=1, offset=[0,0] ) {
    
    var x = node.x
    var y = node.y
    var node_data = node.data

    var stroke = 'black'
    var strokeWidth = 1
    
    // draw label
    if( scale >= .5 ) {
        x = (x+offset[0]) * scale
        y = (y+offset[1]) * scale
        if( isTF(node_data) ){
            var label = node_data.protein_name
            var co = 20
        } else {
            var label = node_data.gene_id
            var co = 8
        }
        
        drawReadableText(ctx,label,x,y,scale)
        //var hpc = node_data.hidden_pdi_count
        //if( hpc > 0 ){
        //    var hidden_report = "(" + hpc.toLocaleString("en-US") + " interactions not shown)"
        //    drawReadableText(ctx,hidden_report,x,y+20,scale);
        //}
    }
}

// draw black text with white shadow
function drawReadableText(ctx,label,x,y){
    ctx.textAlign = 'center';
    ctx.font = '12px Arial'
    ctx.fillStyle = 'white';
    var shadow_radius = 1
    for( var dx = -shadow_radius ; dx <= shadow_radius ; dx++ ){
        for( var dy = -shadow_radius ; dy <= shadow_radius ; dy++ ){
            ctx.fillText(label, x+dx, y+dy);
        }
    }
    ctx.fillStyle = 'black';
    ctx.fillText(label, x, y);
}








