/**
 * @param JSON plasmid
 *
 * Function will draw plasmid Map and replace the canvas with PNG image
 */

function drawThisPlasmid(plasmid){
    plasmid.features.sort(sortByLocation);
    var i = 0;
    $.each(plasmid.features,function(key,obj){
        var colors = ['red','green','blue','orange','violet'];
        obj.color = colors[(i++)%colors.length];
        obj.size = 6;
    });

    plasmid.features.sort(sortByLength);

    var fixer = -75;

    var $canvas = $('#myCanvas');

    var degreePerLength = (360.0) / plasmid.length;
    var radiansPerLength = ( 2 * Math.PI ) / plasmid.length;

    var tickMarkInterval = 100 * Math.round( (plasmid.length / 6) / 100);

    $canvas.drawArc({
        strokeStyle: 'black',
        strokeWidth: 5,
        x: center, y: center,
        radius: radius
    });

    $canvas.drawText({
        fillStyle: 'black',
        fontFamily: 'Ubuntu, sans-serif',
        fontSize: 28,
        text: plasmid.name,
        letterSpacing: 0.008,
        align: 'center',
        baseline: 'bottom',
        x: center, y: center
    });

    drawLargeTickMarks(plasmid.length,radius,tickMarkInterval,center,center);

    var twentyPercent = 0.20 * plasmid.length;

    var allTextStarts = [];
    var allPlotted = [];

    $.each(plasmid.features,function(key,value){
        var midPoint = ( value.end  +  value.start ) / 2.0;
        var angleRadians = (Math.PI/2.0) - ( midPoint * radiansPerLength );

        var textProperties = {
            fillStyle: value.color,
            fontFamily: 'Ubuntu, sans-serif',
            fontSize: 11,
            text: value.name,
            letterSpacing: 0.008,
            align: 'center',
            baseline: 'top',
            maxWidth: 150
        };

        var textWidth = $canvas.measureText(textProperties).width;
        var textHeight = $canvas.measureText(textProperties).height;

        var arcRadius = findNoHitRadius(value.start,value.end,radius,angleRadians,allPlotted);

        var fartherInX = ( ( (arcRadius ) + ( textWidth - 30 ) ) *  Math.cos(angleRadians) ) + center;
        var fartherInY = ( -(arcRadius + ( 30 + textHeight ) ) *  Math.sin(angleRadians)) + center;

        if(arcRadius != radius){
            fartherInX = ( ( (arcRadius ) - ( textWidth ) ) *  Math.cos(angleRadians) ) + center;
            fartherInY = ( ( (-arcRadius + ( 10 + textHeight ) ) *  Math.sin(angleRadians)) + center );
        }

        var cords = findNoHitText(fartherInX,fartherInY,textWidth,textHeight,angleRadians,allTextStarts,1);

        textProperties.x = cords.x;
        textProperties.y = cords.y;

        allTextStarts.push({ x1 : cords.x , y1 : cords.y , x2 : cords.x+textWidth , y2: cords.y + textHeight , text: value.name });

        allPlotted.push({start: value.start , end: value.end, radius: arcRadius });

        $canvas.drawArc({
            strokeStyle: value.color,
            strokeWidth: value.size,
            x: center, y: center,
            start: (value.start*degreePerLength), end: (value.end*degreePerLength),
            radius: arcRadius,
            arrowRadius: value.size * 1.5,
            arrowAngle: 90,
            startArrow: value.strand == -1,
            endArrow: value.strand == 1,
            ccw: false
        });
        $canvas.drawText(textProperties);
    });

    var image = new Image();
    image.src = $('#myCanvas')[0].toDataURL("image/png");
    $('#myCanvas').after(image);
    $('#myCanvas').remove();
}

function sortByLength(a,b){
    var a_length = Math.abs(a.end - a.start);
    var b_length = Math.abs(b.end - b.start);
    if(a_length < b_length){
        return -1;
    }else if(a_length > b_length){
        return 1;
    }else{
        return 0;
    }
}

function sortByLocation(a,b){
    if(a.start < b.start){
        return -1;
    }else if(a.start > b.start){
        return 1;
    }else{
        return 0;
    }
}

function findNoHitRadius(start,end,radius,angleRadians,allPlotted){
    var isOverlap = false;
    $.each(allPlotted,function(val,obj){
        var s1 = start;
        var s2 = end;
        var l1 = obj.start;
        var l2 = obj.end;
        isOverlap = (s2 > (l1 - 30)) && (s1 < (l2 + 30) ) && radius == obj.radius;
        if(isOverlap){
            radius -= 50;
        }

    });

    if(isOverlap){
        return findNoHitRadius(start,end,radius,angleRadians,allPlotted);
    }else{
        return radius;
    }
}

function findNoHitText(r1x1,r1y1,textWidth,textHeight,angleRadians,allTextStarts, iteration){
    var isOverLap = false;

    $.each(allTextStarts,function(val,obj){
        var r2x1 = obj.x1;
        var r2x2 = obj.x2;
        var r2y1 = obj.y1;
        var r2y2 = obj.y2;

        var r1x2 = r1x1 + textWidth;
        var r1y2 = r1y1 + textHeight;

        isOverLap = ((r1x1 < r2x2) && (r1x2 > r2x1) &&  (r1y1 < r2y2) && (r1y2 > r2y1));

        if(isOverLap){
            r1x1 = ( ( (radius ) - ( textWidth - 30 ) ) *  Math.cos(angleRadians) ) + center - (10 * iteration);
            r1y1 = ( -(radius - textHeight  - ( 30 - textHeight ) ) *  Math.sin(angleRadians)) + center - (10 * iteration);
            return;
        }
    });

    if(isOverLap){
        return findNoHitText(r1x1,r1y1,textWidth,textHeight,angleRadians,allTextStarts,iteration+1)
    }else{
        return {x : r1x1 , y: r1y1 };
    }
}

function drawLargeTickMarks(length,radius,step,centerX,centerY){
    var numTicks = length / step
    var radiansPerTick = (2*Math.PI) / numTicks;

    var lines = [];
    for (var i = 0; i < numTicks ; i++){
        var line;

        if(i*step < length - 100 ){
            line = {
                color : i == 0 ? 'Tomato':'black',
                width : i == 0 ? 10:3,
                fromX : radius * Math.cos( ( Math.PI / 2.0 ) - (i*radiansPerTick) ),
                fromY : -radius * Math.sin( ( Math.PI / 2.0 ) - (i*radiansPerTick) ),
                toX : (radius - 10) * Math.cos( ( Math.PI / 2.0 ) - (i*radiansPerTick) ),
                toY : -(radius - 10) * Math.sin( ( Math.PI / 2.0 ) - (i*radiansPerTick) ),
                textX : (radius - 20) * Math.cos( ( Math.PI / 2.0 ) - (i*radiansPerTick) ),
                textY : -(radius - 20) * Math.sin( ( Math.PI / 2.0 ) - (i*radiansPerTick) ),
                text : i == 0 ? length:(i*step)
            };
        }else{
            line = {
                color : 'black',
                width : 3,
                fromX : radius * Math.cos( ( Math.PI / 2.0 ) - ( ( (i * radiansPerTick ) + ( (i-1) * radiansPerTick) ) / 2 ) ),
                fromY : -radius * Math.sin( ( Math.PI / 2.0 ) - ( ( (i * radiansPerTick ) + ( (i-1) * radiansPerTick) ) / 2 ) ),
                toX : (radius - 10) * Math.cos( ( Math.PI / 2.0 ) - ( ( (i * radiansPerTick ) + ( (i-1) * radiansPerTick) ) / 2 ) ),
                toY : -(radius - 10) * Math.sin( ( Math.PI / 2.0 ) - ( ( (i * radiansPerTick ) + ( (i-1) * radiansPerTick) ) / 2 ) ),
                textX : (radius - 20) * Math.cos( ( Math.PI / 2.0 ) - ( ( (i * radiansPerTick ) + ( (i-1) * radiansPerTick) ) / 2 ) ),
                textY : -(radius - 20) * Math.sin( ( Math.PI / 2.0 ) - ( ( (i * radiansPerTick ) + ( (i-1) * radiansPerTick) ) / 2 ) ),
                text : i*step - (step/2)
            };
        }
        lines.push(line);
    }

    $.each(lines,function(key,line){
        $('#myCanvas').drawLine({
            strokeStyle: line.color,
            strokeWidth: line.width,
            x1: line.fromX + centerX, y1: line.fromY + centerY,
            x2: line.toX + centerX, y2: line.toY + centerY
        }).drawText({
                strokeStyle: 'black',
                strokeWidth: 1,
                x: line.textX + centerX , y: line.textY + centerY,
                fontSize: 14,
                fontFamily: 'Verdana, sans-serif',
                text: line.text
            });
    });
}
