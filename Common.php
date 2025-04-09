<?php

namespace App\Helpers;

use App\Helpers\Datatable\SSP;
use App\Models\RequestDocument;
use Illuminate\Pagination\CursorPaginator;

class Common
{
    /**
     * To create a directory if not existed.
     * @param string $path, path of the directory to be created if not existed. 
     * @return bool
     */
    public static function makeDirectory($path, $permission = 0777): void
    {
        if (!is_dir($path)) {
            mkdir($path);
            chmod($path, $permission);
        }
    }
    /**
     * To remove a directory and all its sub folder and files.
     * @param string $path, path of the directory to be removed. 
     * @return bool
     */
    public static function rmdirectory($path): bool
    {
        $dir = opendir($path);
        while ($entry = readdir($dir)) {
            if (is_file("$path/$entry")) {
                unlink($path . '/' . $entry);
            } elseif (is_dir("$path/$entry") && $entry != '.' && $entry != '..') {
                self::rmdirectory("$path/$entry");
            }
        }
        closedir($dir);
        return rmdir($path);
    }

    /**
     * It is used to get existing server file for DropZone JS plugin
     * @param string $requestID, request id or session id. 
     * @return array
     */
    public static function getDZPreview(string $requestID): array
    {
        $requestDocs = RequestDocument::where('session_id', $requestID)->get();
        $DZPreviewData = [];
        $DZPreviewData['count'] = $requestDocs->count();
        $DZPreview = "";
        foreach ($requestDocs as $requestDoc) {

            $fileExtension = pathinfo($requestDoc->document, PATHINFO_EXTENSION);
            if (in_array(strtolower($fileExtension), ['pdf', 'docx', 'doc'])) {
                if (strtolower($fileExtension) == 'pdf') {
                    $docFile = url('assets/frontend/img/pdf-icon.jpg');
                } else {
                    $docFile = url('assets/frontend/img/word-icon.png');
                }
            } else {
                $docFile = REQUEST_DOCUMENT_URL . $requestDoc->session_id . '/' . $requestDoc->document;
            }
            $docFileOriginal = REQUEST_DOCUMENT_URL . $requestDoc->session_id . '/' . $requestDoc->document;
            $DZPreview .= <<<HTML
            <div class="dz-preview dz-file-preview dz-complete">
                <div class="dz-details">
                <a href="{$docFileOriginal}" target="_blank">
                    <div class="dz-thumbnail">
                         <img data-dz-thumbnail
                            src="{$docFile}">
                        <span class="dz-id" data-id="{$requestDoc->id}"></span>
                        <span class="dz-nopreview">No preview</span>
                        <div class="dz-success-mark"></div>
                        <div class="dz-error-mark"></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary" role="progressbar"
                                aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
                        </div>                 
                    </div>
                </a>     
                    <div class="dz-filename" data-dz-name>{$requestDoc->document_original_name}</div>
                    <div class="dz-size" data-dz-size></div>
                </div>
                <a class="dz-remove-existing" href="javascript:undefined;" data-dz-remove="">Remove file</a>
            </div>
            HTML;
        }

        $DZPreviewData['html'] = $DZPreview;

        return $DZPreviewData;
    }

    /**
     * it is used to create custom pagination.
     * @param array $items, collection of items.
     * @param int $perPage, number of records to display in per page.
     * @return CursorPaginator
     */
    public function customCursorPagination(array $items, int $perPage): CursorPaginator
    {
        $pageStart = request()->get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);
        return new CursorPaginator($itemsForCurrentPage, $perPage);
    }

    /**
     * It is used to show request data for a specific memeber
     * @param int $memberId
     * @param mix $limit it is used to limit the record for dashbaorad.
     */
    public static function getRequestStatusData(int $memberId, $limit = null)
    {
        /*
         * DataTables example server-side processing script.
         *
         * Please note that this script is intentionally extremely simply to show how
         * server-side processing can be implemented, and probably shouldn't be used as
         * the basis for a large complex system. It is suitable for simple use cases as
         * for learning.
         *
         * See http://datatables.net/usage/server-side for full details on the server-
         * side processing requirements of DataTables.
         *
         * @license MIT - http://datatables.net/license_mit
         */

        /*         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * Easy set variables
         */

        // DB table to use
        $table = 'requests';

        // Table's primary key
        $primaryKey = 'id';

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = array(
            array('db' => 'request_types.name', 'dt' => 0, 'field' => 'name'),
            array('db' => 'requests.created_at', 'dt' => 1, 'formatter' => function ($d, $row) {
                return date('d M Y', strtotime($d));
            }, 'field' => 'created_at'),
            array('db' => 'requests.status', 'dt' => 2, 'formatter' => function ($d, $row) {
                if ($d == 1) {
                    return '<span class="badge process-badge">Processing</span>';
                } else if ($d == 2) {
                    return '<span class="badge darkgreen-badge">COMPLETED</span>';
                }
                return '';
            }, 'field' => 'status'),
            array('db' => 'requests.updated_at', 'dt' => 3, 'formatter' => function ($d, $row) {
                return date('d M Y', strtotime($d));
            }, 'field' => 'updated_at'),
            array('db' => 'requests.id', 'dt' => 4, 'field' => 'id'),
           
        );

        // SQL server connection information
        $sql_details = array(
            'user' => config('database.connections.mysql.username'),
            'pass' => config('database.connections.mysql.password'),
            'db' => config('database.connections.mysql.database'),
            'host' => config('database.connections.mysql.host')
        );


        /*         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        //require('ssp.customized.class.php' );

        $joinQuery = " JOIN request_types ON request_types.id = requests.type_id ";
        $extraWhere = " requests.member_id = " . $memberId;
        $groupBy = "";

        //it is used for listing of request at dashboard page where we set pagination false so we need to overwrite it.
        //default value for length is -1 when pagination is set to false.
        if($limit){
            $_GET['length'] = 3;
        }

        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy)
        );
    }
}
