<?php
/**
* @developer Junaid Ahmed
* @purpose A helper to make customized log file for debug the errors.
* @developedOn March 2015
*/

/**
 * @param $data <p> Contents to be printed </p>
 * @param string $label <p> Any label </p>
 * @param string $type <p> Can be input, output and debug </p>
 * @return bool
 */
function logIt($data, $label = '', $type = 'debug')
{
    if(MAKE_LOG == FALSE) return;

    $defaultTZ = date_default_timezone_get();
    date_default_timezone_set('Asia/Karachi');

    $file = dirname(dirname(__FILE__)) . "/logs/custom.log";
    $fp = fopen($file, "a+");
    if (!$fp) {
        error_log("Unable to create log file.");
        return false;
    }

    //region Default Debug State
    if($type == 'debug') {
        // Store dump in variable
        ob_start();
        //var_dump( $var );
        print_r( $data );
        $output = ob_get_clean();
        $label  = $label ? '<strong>'.$label . '</strong> ' : '';

        //region Location and line-number
        $caller    = debug_backtrace();
        if ( count( $caller ) > 0 ) {
            $tmp_r = $caller[ 0 ];
        }
        //endregion

        $output = '
        <div class="container">
            <div class="head">
                <strong style="color: lime;">( '.$label.' )</strong>

                &nbsp;&nbsp;&nbsp;
                <strong style="color:#000;">Dated:</strong>
                <span >'.date('M d, Y h:i:s').'</span>

                &nbsp;&nbsp;&nbsp;
                <strong style="color:#000;">Location:</strong>
                <span>'.$tmp_r["file"].'</span> ( '.$tmp_r["line"].' )

                <a href="#" class="toggle-icon" style="float:right;">+</a>
            </div>
            <div class="data dn"><pre class="snippet">'.$output.'</pre></div>
        </div>
        ';

        fwrite($fp, $output);
    }
    //endregion

    //region simple format
    if($type == 'simple') {
        // Store dump in variable
        ob_start();
        //var_dump( $var );
        print_r( $data );
        $output = ob_get_clean();
        $label  = $label ? '<strong>'.$label . '</strong> ' : '';

        // Location and line-number
        $line      = '';
        $separator = "<strong style='color:blue'>" . str_repeat( "-", 143 ) . "</strong>" . PHP_EOL;
        $caller    = debug_backtrace();
        if ( count( $caller ) > 0 ) {
            $tmp_r = $caller[ 0 ];
            $line .= "<strong style='color:blue'>Dated:</strong> => <span style='color:red'>" . date('M d, Y H:i:s') . '</span> ';
            $line .= "<strong style='color:blue'>Location:</strong> => <span style='color:red'>" . $tmp_r[ 'file' ] . '</span>';
            $line .= " (" . $tmp_r[ 'line' ] . ')';
        }

        // Add formatting
        $output = preg_replace( "/\]\=\>\n(\s+)/m", "] => ", $output );
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">'
        . $label
        . $line
        . PHP_EOL
        . $separator
        . $output
        . '</pre>';

        // Output
        fwrite($fp, $output);
    }
    //endregion

    fclose($fp);

    date_default_timezone_set($defaultTZ);
}

function logItX($data, $label = '', $type = '')
{
    $file = dirname(dirname(__FILE__)) . "/logs/custom.log";
    $fp = fopen($file, "a+");
    if (!$fp) {
        error_log("Unable to create log file.");
        return false;
    }

    //writing data into log file
    if ($type == 'output') {
        fwrite($fp, "<strong>[OUTPUT]</strong>\n");
    } else {
        $label = "<strong>[" . date('m-d-Y H:i:s') . "] $label</strong>";
        fwrite($fp, $label . "\n================================================================================================================================\n");
    }

    if ($type == 'input') {
        fwrite($fp, "<strong>[INPUT]</strong>\n");
    }

    $data = is_array($data) ? print_r($data, true) : $data;
    fwrite($fp, $data);

    if ($type == 'input') {
        fwrite($fp, "\n\n");
    } else {
        fwrite($fp, "\n================================================================================================================================\n");
        fwrite($fp, "\n\n\n\n");
    }

    fclose($fp);
}

// Show Log
function showLog()
{
    $log_data = '';

    // Log file
    $log_file = @file(dirname(dirname(__FILE__)) . "/logs/custom.log");
    $count = count($log_file);

    //$log_data .= '<pre>';

    $log_data = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Log System</title>
            <style type="text/css">
            *{font-family: arial; font-size: 12px;}
            .container{
                background: #949494; color: #000;
                width: 100%;
                margin-bottom: 4px;
                margin-top: 2px;
                box-shadow: 2px 2px #CDCDCD;
            }
            body{margin-top: 2px;}
            .head{
                background: rgb(53,106,160); /* Old browsers */
                background: -moz-linear-gradient(top,  rgba(53,106,160,1) 0%, rgba(53,106,160,1) 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(53,106,160,1)), color-stop(100%,rgba(53,106,160,1))); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(top,  rgba(53,106,160,1) 0%,rgba(53,106,160,1) 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(top,  rgba(53,106,160,1) 0%,rgba(53,106,160,1) 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(top,  rgba(53,106,160,1) 0%,rgba(53,106,160,1) 100%); /* IE10+ */
                background: linear-gradient(to bottom,  rgba(53,106,160,1) 0%,rgba(53,106,160,1) 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#356aa0", endColorstr="#356aa0",GradientType=0 ); /* IE6-9 */
            }
            .head{ padding: 6px; color: #fff; cursor:pointer; }
            .data{ background: #DEF0FF; padding: 6px; display: block; width: 99%; }
            .dn{ display: none;}
            .db{ display: block;}
            .toggle-icon{text-decoration: none; color: #000; margin-right: 10px; color: #fff;}
            .buttons{ width: 100%; position: relative; padding-bottom: 3px;}
            .menu { display: table; margin: 0 auto;}
            .menu li{ float: left; display: block; margin: 0 100px;}
            .menu li a{ display: block; }

            .toggle-all {
			  background: #3498db;
			  background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
			  background-image: -moz-linear-gradient(top, #3498db, #2980b9);
			  background-image: -ms-linear-gradient(top, #3498db, #2980b9);
			  background-image: -o-linear-gradient(top, #3498db, #2980b9);
			  background-image: linear-gradient(to bottom, #3498db, #2980b9);
			  -webkit-border-radius: 28;
			  -moz-border-radius: 28;
			  border-radius: 28px;
			  font-family: Arial;
			  color: #ffffff !important;
			  font-size: 14px;
			  padding: 10px 20px 10px 20px;
			  text-decoration: none;
			}
			.toggle-all:hover {
			  background: #3cb0fd;
			  background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
			  background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
			  background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
			  background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
			  background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
			  text-decoration: none;
			}

			.clear-log {
			  background: #d93434;
			  background-image: -webkit-linear-gradient(top, #d93434, #b82b2b);
			  background-image: -moz-linear-gradient(top, #d93434, #b82b2b);
			  background-image: -ms-linear-gradient(top, #d93434, #b82b2b);
			  background-image: -o-linear-gradient(top, #d93434, #b82b2b);
			  background-image: linear-gradient(to bottom, #d93434, #b82b2b);
			  -webkit-border-radius: 28;
			  -moz-border-radius: 28;
			  border-radius: 28px;
			  font-family: Arial;
			  color: #ffffff !important;
			  font-size: 14px;
			  padding: 10px 20px 10px 20px;
			  text-decoration: none;
			}
			.clear-log:hover {
			  background: #fc3c3c;
			  background-image: -webkit-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: -moz-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: -ms-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: -o-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: linear-gradient(to bottom, #fc3c3c, #d93434);
			  text-decoration: none;
			}

			.refresh-log {
			  background: #34d960;
			  background-image: -webkit-linear-gradient(top, #34d960, #2cb864);
			  background-image: -moz-linear-gradient(top, #34d960, #2cb864);
			  background-image: -ms-linear-gradient(top, #34d960, #2cb864);
			  background-image: -o-linear-gradient(top, #34d960, #2cb864);
			  background-image: linear-gradient(to bottom, #34d960, #2cb864);
			  -webkit-border-radius: 28;
			  -moz-border-radius: 28;
			  border-radius: 28px;
			  font-family: Arial;
			  color: #ffffff !important;
			  font-size: 14px;
			  padding: 10px 20px 10px 20px;
			  text-decoration: none;
			}
			.refresh-log:hover {
			  background: #3cfa62;
			  background-image: -webkit-linear-gradient(top, #3cfa62, #34d960);
			  background-image: -moz-linear-gradient(top, #3cfa62, #34d960);
			  background-image: -ms-linear-gradient(top, #3cfa62, #34d960);
			  background-image: -o-linear-gradient(top, #3cfa62, #34d960);
			  background-image: linear-gradient(to bottom, #3cfa62, #34d960);
			  text-decoration: none;
			}
            pre.snippet {
                word-wrap: break-word;
            }
            </style>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
            <script>
                $(function() {
                    $(".toggle-all").click(function(event){
                        $(".data").slideToggle();
                        event.preventDefault();
                    });
                    $(".head").click(function(){
                        $(this).next(".data").slideToggle();

                        var icon = $(this).find(".toggle-icon").html()
                        if(icon == "+") {
                            $(this).find(".toggle-icon").html("-");
                        } else {
                            $(this).find(".toggle-icon").html("+");
                        }
                    });
                });
            </script>
        </head>
        <body>

        <div class="button">
            <ul class="menu">
                <li><a href="'.base_url("log/show").'" class="refresh-log" style="text-decoration: none; color: green;">Refresh Log</a></li>
                <li><a href="#" class="toggle-all" style="text-decoration: none; color: lime;">Toggle All</a></li>
                <li><a href="'.base_url("log/clear").'" class="clear-log" style="text-decoration: none; color: red;">Clear Log</a></li>
            </ul>
        </div>
    ';

    for ($i = 0; $i < $count; $i++) {
        $log_data .= $log_file[$i];
    }
    //$log_data .= '</pre>';
    $log_data .= '</body></html>';

    return $log_data;
}

// Clear Log
function clearLog()
{
    $file = dirname(dirname(__FILE__)) . "/logs/custom.log";
    @unlink($file);
}
