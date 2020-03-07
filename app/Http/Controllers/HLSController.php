<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use SergeyHartmann\StreamingLinkGenerator\Generator;
use SergeyHartmann\StreamingLinkGenerator\StreamingLink;
use SergeyHartmann\StreamingLinkGenerator\CookieLoader\SimpleCookieLoader;

use Illuminate\Support\Facades\Cache;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\RedirectMiddleware;
use GuzzleHttp\RequestOptions;

class HLSController extends Controller
{

    public function redirectToStreamLink($rStart, $rEnd, $fileId, Request $request) {
        $link;
        if (!Cache::has($fileId)) {
            $cookieLoader = new SimpleCookieLoader(public_path('g.cookie'));
            $generator    = new Generator($cookieLoader);
            $streamingLink = $generator->generate($fileId);
            $link = $streamingLink->getStreamingLink();
            Cache::put($fileId, $link, 20*60);
        } else {
            $link = Cache::get($fileId);
        }
        return redirect($link);
    }

    public function combineFile() {
        $files = Storage::disk('converted_videos')->files('Test/Test_Video');
        $m3u8File = Storage::disk('converted_videos')->get('Test/Test_Video/EncryptedDocument_T5_1500.m3u8');
        $content = "";
        $fileSize = -1;
        $fileIndex = 0;
        foreach($files as $file) {
            $fileName = explode("/", $file);
            if (Storage::disk('converted_videos')->size($file) > 100*1024) {
                // dump("file".$fileIndex." ".($fileSize + 1)." ".($fileSize + Storage::disk('converted_videos')->size($file)));
                $m3u8File = str_replace($fileName[count($fileName) - 1], "file".$fileIndex." ".($fileSize + 1)." ".($fileSize + Storage::disk('converted_videos')->size($file)), $m3u8File);
                $fileSize = $fileSize + Storage::disk('converted_videos')->size($file);
                $content = $content.Storage::disk('converted_videos')->get($file);
                if ($fileSize > (12*1024*1024)) {
                    Storage::disk('converted_videos')->put('Test/Test_Video'.'/TEST/file'.$fileIndex, $content);
                    $content = "";
                    $fileSize = -1;
                    $fileIndex++;
                }
            }
        }
        if (!empty($content)) {
            Storage::disk('converted_videos')->put('Test/Test_Video'.'/TEST/file'.$fileIndex, $content);
        }
        Storage::disk('converted_videos')->put('Test/Test_Video/index.m3u8', $m3u8File);
    }

    public function test() {
        $client = new Client();
        $confirmCode;
        $jar;

        $response = $client->get("https://drive.google.com/uc?export=download&id=1gcNBjuNrDu5S3eu0nxazB3Frz-gBXLSM");
  
        if ($response->getStatusCode() == 200) {
  
            $cookie = $response->getHeader('set-cookie')[0];
            $cookieJar = SetCookie::fromString($cookie);
            $jar = new CookieJar();
            $jar->setCookie(SetCookie::fromString($cookie));
            if (strpos($cookieJar->getName(), "download_warning_") !== false) {
                $confirmCode = $cookieJar->getValue();
                $client2 = new Client(['cookies' => $jar]);
                $response2 = $client2->get('https://drive.google.com/uc?export=download&id=1gcNBjuNrDu5S3eu0nxazB3Frz-gBXLSM&confirm='.$confirmCode);
               
                // $f_remote = fopen($response2->getBody()->getContents(), 'rb');
                $f_local = fopen('test', 'w');
                $read = 0;
                while (!$response2->getBody()->eof()) {
                    $chunk = $response2->getBody()->read(2048);
                    fwrite($f_local, $chunk);
                    $read += strlen($chunk);
                    echo $read.PHP_EOL;
                }

                fclose($f_local);
            }
        }

    }

    public function getPlayer(Request $request) {
        $m3u8 = $request['m3u8'];
        return view('player', ['m3u8' => $m3u8]);
    }

    public function registerLika() {
        $client = new Client();
        $content = "";
        for ($i=1; $i <= 6; $i++) { 
            for ($j=0; $j <= 5 ; $j++) { 
                $lop_id = 4 + $i;
                $phone_nummber = 65 + ($i * ($j + 1));
                $response = $client->post("https://lika.edu.vn/register/ajax-create-trai-nghiem", [
                    'form_params' => [
                        'ten_ph' => 'Nguyen Van A',
                        'name' => 'Nguyen Van A',
                        'username' => '07060836'.$phone_nummber,
                        'password' => '07060836'.$phone_nummber,
                        'nameStudent' => 'Nguyen Dinh A',
                        'ten_hs' => 'Nguyen Dinh A',
                        'phone' => '07060836'.$phone_nummber,
                        'code' => null,
                        'email' => 'nhqtrung'.$phone_nummber.'@gmail.com',
                        'lop_id' => $lop_id,
                        'type' => 'free',
                        'type_tk' => 5,
                        'month' => 12
                    ]
                ]);
                if ($response->getStatusCode() == 200) {
                    $content = $content."07060836$phone_nummber $i".PHP_EOL;
                }
            }
        }
        Storage::disk('videos')->put('lika-account.txt', $content);
    }

    public function crawlLika() {
        $client = new Client();
        $loginCookies = "";
        $response = $client->post("https://lika.edu.vn/dang-nhap.html", [
            'form_params' => [
                'username' => '07060836101',
                'password' => '07060836101'
            ]
        ]);
        if ($response->getStatusCode() < 400) {
            if ($cookies = $response->getHeader('set-cookie')) {
                $jar = new CookieJar();
                foreach ($cookies as $cookie) {
                    $jar->setCookie(SetCookie::fromString($cookie));
                    dump($cookie);
                    $loginCookies = $loginCookies;
                }
            }
            $options = [
                // RequestOptions::VERIFY          => false,
                RequestOptions::COOKIES         => $loginCookies
                // RequestOptions::ALLOW_REDIRECTS => [
                //     'max'             => 5,
                //     'track_redirects' => true,
                //     'on_redirect'     => $onRedirect,
                // ]
            ];
            $response = $client->get("https://lika.edu.vn/lika/index-member", $options);

            dump($response->getBody()->getContents());
        }

    }

}
