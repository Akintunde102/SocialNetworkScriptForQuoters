                <?php 
			class imdet
{
	Public Function edit($details){
				        include_once 'Toolkit_Version.php'; 
                      
                        include_once 'Toolkit_Version.php';  // Change: added as of version 1.11

                        // Include the required files for reading and writing Photoshop File Info
                        include_once 'JPEG.php';
                        include_once 'XMP.php';
                        include_once 'Photoshop_IRB.php';
                        include_once 'EXIF.php';
                        include_once 'Photoshop_File_Info.php';

                        // Copy all of the details into an array
                        $new_ps_file_info_array = $details;

                        // Keywords should be an array - explode it on newline boundarys
                        $new_ps_file_info_array[ 'keywords' ] = explode( "\n", trim( $new_ps_file_info_array[ 'keywords' ] ) );

                        // Supplemental Categories should be an array - explode it on newline boundarys
                        $new_ps_file_info_array[ 'supplementalcategories' ] = explode( "\n", trim( $new_ps_file_info_array[ 'supplementalcategories' ] ) );

                        // Make the filename easier to access
                        $filename = $new_ps_file_info_array['filename'];

                        // Protect against hackers editing other files
                        $path_parts = pathinfo( $filename );
						 if ( strcasecmp( $path_parts["extension"], "jpg" ) != 0 )
                        {
                                echo "Incorrect File Type - JPEG Only\n";
                                exit( );
                        }
                        
                        // Retrieve the header information
                        $jpeg_header_data = get_jpeg_header_data( $filename );

                        // Retreive the EXIF, XMP and Photoshop IRB information from
                        // the existing file, so that it can be updated
                        $Exif_array = get_EXIF_JPEG( $filename );
                        $XMP_array = read_XMP_array_from_text( get_XMP_text( $jpeg_header_data ) );
                        $IRB_array = get_Photoshop_IRB( $jpeg_header_data );

                        // Update the JPEG header information with the new Photoshop File Info
                        $jpeg_header_data = put_photoshop_file_info( $jpeg_header_data, $new_ps_file_info_array, $Exif_array, $XMP_array, $IRB_array );

                        // Check if the Update worked
                        if ( $jpeg_header_data == FALSE )
                        {
                                // Update of file info didn't work - output error message
                                echo "Error - Failure update Photoshop File Info : $filename <br>\n";
						}
                        // Attempt to write the new JPEG file
                        if ( FALSE == put_jpeg_header_data( $filename, $filename, $jpeg_header_data ) )
                        {
                                // Writing of the new file didn't work - output error message
                                echo "Error - Failure to write new JPEG : $filename <br>\n";
						}
	}
	
	
	Public Function retrieve($filename){
				     
					 include_once 'Toolkit_Version.php';          // Change: added as of version 1.11

                // Hide any unknown EXIF tags
                $GLOBALS['HIDE_UNKNOWN_TAGS'] = TRUE;

                // Accessing the existing file info for the specified file requires these includes
                include_once 'JPEG.php';
                include_once 'XMP.php';
                include_once 'Photoshop_IRB.php';
                include_once 'EXIF.php';
                include_once 'Photoshop_File_Info.php';

                // Retrieve the header information from the JPEG file
                $jpeg_header_data = get_jpeg_header_data( $filename );

                // Retrieve EXIF information from the JPEG file
                $Exif_array = get_EXIF_JPEG( $filename );

                // Retrieve XMP information from the JPEG file
                $XMP_array = read_XMP_array_from_text( get_XMP_text( $jpeg_header_data ) );

                // Retrieve Photoshop IRB information from the JPEG file
                $IRB_array = get_Photoshop_IRB( $jpeg_header_data );

                // Retrieve Photoshop File Info from the three previous arrays
                $new_ps_file_info_array = get_photoshop_file_info($Exif_array, $XMP_array, $IRB_array );     
				
				return $new_ps_file_info_array;

	}
	
	
	
}
					  ?>