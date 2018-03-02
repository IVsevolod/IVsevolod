<?php


namespace common\components;


use common\models\Vkpost;
use CURLFile;
use yii\base\Component;

class VkontakteComponent extends Vkontakte
{


    public function init()
    {
        parent::init();

    }

    public function __construct($config = [])
    {
        if (isset($config['accessToken'])) {
            $this->setAccessToken($config['accessToken']);
        }
    }

    public function saveImg($url)
    {
        $tmpfname = tempnam("/tmp", "FOO");

        $handle = fopen($tmpfname, "w");
        fwrite($handle, file_get_contents($url));
        fclose($handle);

        return $tmpfname;
    }

    public function initAccessToken($accessToken)
    {
        $this->setAccessToken(json_encode(['access_token' => $accessToken]));
    }

    public function vkPost($publickId)
    {
        $urlImg = $this->saveImg('http://brazilianzouk.ru/img/logo.png');
        if ($this->postToPublic($publickId, "Привет Хабр!", $urlImg, [])) {
            unlink($urlImg);
            return true;
        } else {
            unlink($urlImg);
            return false;
        }
    }

    public function vkGet($groupId, $groupName, $offset, $limit)
    {
        $params = [
            'offset'   => $offset,
            'count'    => $limit,
            'owner_id' => '',
            'domain'   => '',
        ];
        if (empty($groupId)) {
            $params['owner_id'] = -$groupId;
        } else {
            $params['domain'] = $groupName;
        }

        return $this->api('wall.get', $params);
    }

    /**
     * @param int $groupId
     * @param int $publishDate
     * @param Vkpost $vkpost
     * @param string[] $tags
     *
     * @return \stdClass
     */
    public function vkPostFromModel($groupId, $publishDate, $vkpost, $tags)
    {
        $text = $vkpost->text;
        $breaks = array("<br />","<br>","<br/>");
        $text = str_ireplace($breaks, "\r\n", $text);

        $attachments = $vkpost->attachments;
        if (empty($attachments)) {
            $attachments = "";
        } else {
            $attachmentsObj = json_decode($attachments, true);
            $attachments = [];
            foreach ($attachmentsObj ?? [] as $value) {
                $valueData = $value[$value['type']];
                if (isset($value['type']) && $value['type'] == 'doc') {
                    $response = $this->api('docs.getUploadServer', [
                        'group_id' => $groupId,
                    ]);
                    if (empty($response->upload_url)) {
                        continue;
                    }
                    $uploadURL = $response->upload_url;

                    $tmpfname = tempnam("/tmp", "doc");
                    $handle = fopen($tmpfname, "w");
                    fwrite($handle, file_get_contents($value['doc']['url'], FILE_USE_INCLUDE_PATH));
                    fclose($handle);

                    $newFileName = "all_humors_" . substr(md5(basename($tmpfname)), 0, 8) . '.gif';
                    $finfo = finfo_open(FILEINFO_MIME);
                    $mime = finfo_file($finfo, $tmpfname);
                    $parts = explode(";", $mime);
                    $file = new CurlFile($tmpfname, array_shift($parts), $newFileName);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $uploadURL);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,  ['file' => $file]);

                    $response = json_decode(curl_exec($ch));
                    curl_close($ch);

                    unlink($tmpfname);

                    $response = $this->api('docs.save', [
                        'file' => $response->file,
                    ]);
                    if (!empty($response) && !empty($response[0]) && isset($response[0]->id)) {
                        $attachments[] = $value['type'] . $response[0]->owner_id . '_' . $response[0]->id;
                    }
                } else if (isset($value['type']) && $value['type'] == 'photo') {
                    $response = $this->api('photos.getWallUploadServer', [
                        'group_id' => $groupId,
                    ]);
                    if (empty($response->upload_url)) {
                        continue;
                    }
                    $uploadURL = $response->upload_url;

                    $tmpfname = tempnam("/tmp", "photo");
                    $handle = fopen($tmpfname, "w");
                    fwrite($handle, SimpleHtmlDom::get_content($value['photo']['src_big']));
//                    fwrite($handle, file_get_contents($value['photo']['src_big'], FILE_USE_INCLUDE_PATH));
                    fclose($handle);

                    $finfo = finfo_open(FILEINFO_MIME);
                    $mime = finfo_file($finfo, $tmpfname);
                    $parts = explode(";", $mime);
                    $file = new CurlFile($tmpfname, array_shift($parts), 'image.jpg');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $uploadURL);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,  ['file1' => $file]);

                    $response = json_decode(curl_exec($ch));
                    curl_close($ch);

                    unlink($tmpfname);

                    $response = $this->api('photos.saveWallPhoto', [
                        'group_id' => $groupId,
                        'photo' => $response->photo,
                        'server' => $response->server,
                        'hash' => $response->hash,
                    ]);
                    if (!empty($response) && !empty($response[0]) && isset($response[0]->id)) {
                        $attachments[] = 'photo' . $response[0]->owner_id . '_' . $response[0]->id;
                    }
                } else if (isset($value['type']) && isset($valueData['owner_id']) && isset($valueData['pid'])) {
                    $attachments[] = $value['type'] . $valueData['owner_id'] . '_' . $valueData['pid'];
                }
            }
            $attachments = join(',', $attachments);
        }
        if (!empty($tags)) {
            $text .= "\n\n";

            foreach ($tags as $tag) {
                $text .= ' #' . str_replace(' ', '_', $tag);
            }
        }
        $params = [
            'owner_id'     => -$groupId,
            'message'      => $text,
            'from_group'   => 1,
            'publish_date' => $publishDate,
            'guid'         => date('Ym') . (empty($vkpost->post_id) ? date('dHis') : $vkpost->post_id ),
            'attachments'  => $attachments,
        ];

        return $this->apiPost('wall.post', $params);
    }

}