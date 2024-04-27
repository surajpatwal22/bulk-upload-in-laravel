<?php

namespace App\Http\Controllers;


use App\Models\Singer;
use App\Models\Song;
use Exception;
use Illuminate\Http\Request;

class BulkUploadController extends Controller
{
    public function bulkUploadSongs(Request $request){
        // dd($request);
        try {
            $filePath= $request->file('import');
            $file = fopen($filePath, 'r');
            // dd($file , $filePath );
            if (!$file) {
                throw new Exception('Unable to open the CSV file.');
            }
            if($file){
                $header = fgetcsv($file);
                $data = [];

                while (($row = fgetcsv($file)) !== false) {
                    $rowData = array_combine($header, $row);
        
                    $data[] = $rowData;
                }

                fclose($file);
                $errorarray = [];
                // dd($data);
                foreach ($data as $row){
                    
                    $singer_id = $this->getSingerId($row['singer']);
                    $releaseDate = date('Y-m-d', strtotime($row['release_date']));


                    Song::create([
                        'title' => $row['title'],
                        'subtitle' => $row['subtitle'],
                        'file' => $row['file'],
                        'image' => $row['image'],
                        'status' => $row['status'],     
                        'upc_no' => $row['upc_no'],
                        'isrc_no' => $row['isrc_no'],
                        'catalog_no' => $row['isrc_no'],
                        'singer_id' => $singer_id,
                        'album_id' => $row['album_id'],
                        'mood_id' =>$row['mood'],
                        'language_id' => $row['language'],
                        'genre_id' => $row['genre'] ,
                        'music_director_id' => $row['music_director'] ,
                        'year' => $row['year'],
                        'release_date' => $releaseDate,
                        'lyrics' => $row['lyrics'],
                    ]);
                   
                    
                   return redirect()->back()->with('status', 'Songs uploaded successfully.');
                }
            }
        } catch (Exception $e) {
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

   
    private function getSingerId($singerName)
    {
        
        $singer = Singer::where('name','REGEXP','[[:<:]]'.$singerName.'[[:>:]]')->first();
    
        return $singer ? $singer->id : null;
    }
     
}



