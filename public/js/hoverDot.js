/**
 * Created by (c) Sebastian Kaim, 2016
 *
 * This file is licensed under the MIT License.
 */
"use strict";

(function ($) {

    // dot helper class
    var dot = function (_x, _y, _text, _color) {
        this.x = _x;
        this.y = _y;
        this.text = _text;
        this.color = _color;
        this.radius = 24; // hover event radius

        this.render = function (context) {
            context.beginPath();
            context.arc(this.x, this.y, 4, 0, Math.PI * 2, true);
            context.fillStyle = this.color;
            context.fill();
        };
    };

    // register the plugin
    $.fn.dotHover = function (dots, options) {

        // settings
        var settings = $.extend({
            // defaults

            // this should be a valid image url which will be used as background
            img: "karte.png",

            // whether to allow the user to place dots on click
            setmode: false,

            // a callback for when an event is created
            setcallback: function (dot) {},

            // default text for an unitialized dot
            defaulttext: "Example Text"

        }, options );

        // initialize dots
        settings.dots = [];
        $.each(dots, function (i, el) {
            settings.dots.push(new dot(el.x, el.y, el.text, el.color));
        });

        //
        // create tooltip canvas
        //
        var tooltip = $('<div/>')
            .appendTo('body')
            .addClass('hoverDotTooltip')
            .css({
                backgroundColor: 'rgba(0,0,0,.8)',
                border: '1px solid #fff',
				padding: '5px',
                position: 'absolute',
                padding: '5px',
                marginLeft: '-50px',
				color:'#fff'
            })
            .hide();

        var contexts = [];

        var showHideTooltip = function (show) {
            if(show)
            {
                tooltip.show();
                tooltip.get(0).style.marginLeft = ((-tooltip.width() / 2) + 3) + "px";
            }
            else
            {
                tooltip.hide();
            }
        };

        //
        // handle tooltip positioning on mouse move
        //
        var mouseMoveEvent = function(event, img, offsetX, offsetY) {
            var mouseX = parseInt(event.clientX - offsetX);
            var mouseY = parseInt(event.clientY - offsetY + $(window).scrollTop());

            var hit = false;
            for (var i = 0; i < settings.dots.length; i++) {
                var dot = settings.dots[i];
                var dx = mouseX - dot.x;
                var dy = mouseY - dot.y;

                if (dx * dx + dy * dy < dot.radius) {
                    tooltip.get(0).style.top = ((dot.y - 40) + offsetY) + "px";
                    tooltip.get(0).style.left = (dot.x + offsetX) + "px";

                    tooltip.html(dot.text);
                    hit = true;
                }
            }

            showHideTooltip(hit);
        };

        // re-renders all dots
        var render = function () {
            $.each(contexts, function (i, data) {
                $.each(settings.dots, function (j, dot) {
                    dot.render(data.ctx);
                });
            });
        };

        //
        // places a new dot
        //
        var placedot = function (event, element) {
            var ndot = new dot(event.clientX - element.offsetLeft, event.clientY - element.offsetTop + $(window).scrollTop(), settings.defaulttext); // -7,-7 for cursor offset

            settings.dots.push(ndot);
            render();
            settings.setcallback(ndot);
        };

        //
        // removes a dot
        //
        this.removeDot = function(x, y)
        {
            settings.dots = settings.dots.filter(function(el, index) {
                return dot.x != x && dot.y != y;
            });
        };

        // init mouse move on each element
        this.each(function(i, el) {
            el.style= 'background: url(' + settings.img + ');' +
            'background-repeat: no-repeat; ' +
            'background-size: contain;' +
            'width: ' + settings.width + '; ';
            //'height: ' + settings.height + '; '

            contexts.push( { el: el, ctx: el.getContext('2d') });

            $(el).mousemove (function (event) {
                mouseMoveEvent(event, el, el.offsetLeft, el.offsetTop);
            });

            if(settings.setmode) $(el).click(function (e) {
                placedot(e, el);
            });
        });

        // render initial dots
        render();

        return this;
    };

})(jQuery);
