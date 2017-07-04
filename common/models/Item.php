<?php
namespace common\models;

use common\models\User;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Item model
 *
 * @property integer     $id
 * @property integer     $user_id
 * @property string      $title
 * @property string      $description
 * @property int         $like_count
 * @property int         $show_count
 * @property string      $alias
 * @property string      $entity_type
 * @property string      $source_url
 * @property int         $deleted
 * @property integer     $date_update
 * @property integer     $date_create
 *
 * @property TagEntity[] $tagEntity
 * @property TagEntity[] $tagEntityLibrary
 * @property User        $user
 */
class Item extends VoteModel
{

    const THIS_ENTITY = 'item';

    const ENTITY_TYPE_ITEM = 'item';
    const ENTITY_TYPE_LIBRARY = 'library';

    const MAX_IMG_ITEM   = 5;
    const MAX_VIDEO_ITEM = 5;
    const MAX_SOUND_ITEM = 10;

    const MIN_REPUTAION_BAD_ITEM_DELETE                    = 10;
    const MIN_REPUTATION_ITEM_CREATE                       = -4;
    const MIN_REPUTATION_ITEM_VOTE                         = -4;
    const MIN_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = -3;
    const MAX_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE = 100;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }

    public function getTitle()
    {
        return htmlspecialchars($this->title);
    }

    public function getTitle2()
    {
        return strip_tags($this->title);
    }

    public function getShortDescription($length = 500, $end = '...')
    {
        $charset = 'UTF-8';
        $token = '~';
        $description = $this->description;
        $description = preg_replace("'<blockquote[^>]*?>.*?</blockquote>'si", " ", $description);
        $str = strip_tags($description);
        $str = str_replace("\n", ' ', $str);
        $str = str_replace("\r", ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        if (mb_strlen($str, $charset) >= $length) {
            $wrap = wordwrap($str, $length, $token);
            $str_cut = mb_substr($wrap, 0, mb_strpos($wrap, $token, 0, $charset), $charset);
            $str_cut .= $end;
            return $str_cut;
        } else {
            return $str;
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'date_update'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'default', 'value' => ''],
            [['title'], 'required'],
            [['date_update', 'date_create'], 'integer'],
            [['title', 'alias', 'source_url', 'entity_type'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 50000],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'userId'      => 'Пользователь',
            'title'       => 'Заголовок',
            'description' => 'Описание',
            'likeCount'   => 'Голосов',
            'showCount'   => 'Показов',
            'alias'       => 'Алиас',
            'date_update' => 'Date Update',
            'date_create' => 'Date Create',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTagEntity()
    {
        return $this->hasMany(TagEntity::className(), ['entity_id' => 'id'])->andOnCondition([TagEntity::tableName() . '.entity' => TagEntity::ENTITY_ITEM]);
    }

    public function getTagEntityLibrary()
    {
        return $this
            ->hasMany(TagEntity::className(), ['entity_id' => 'id'])
            ->andOnCondition([
                TagEntity::tableName() . '.entity' => TagEntity::ENTITY_LIBRARY,
            ]);
    }

    public function addVote($changeVote)
    {
        $this->like_count += $changeVote;
    }

    public function getVoteCount()
    {
        return $this->like_count;
    }

    public function saveTags($tags, $tagGroup = Tags::TAG_GROUP_ALL, $tagEntityType = TagEntity::ENTITY_ITEM)
    {
        foreach ($tags as $tag) {
            TagEntity::addTag(trim($tag), $tagGroup, $tagEntityType, $this->id);
        }
    }

    public function addShowCount()
    {
        $this->show_count++;
        $this->save();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->alias == "") {
                $title = $this->encodestring($this->title);
                $alias = $this->toAscii($title);
                $baseAlias = substr($alias, 0, 250);
                $alias = $baseAlias;
                $i = 1;
                $wheres = ['alias = :alias'];
                $params[':alias'] = $alias;
                if (!is_null($this->id)) {
                    $wheres[] = 'id <> :id';
                    $params = [':id' => $this->id];
                }
                $where = join(' AND ', $wheres);
                while ($findItem = Item::find()->where($where, $params)->one()) {
                    $alias = $baseAlias . '-' . $i;
                    $params[':alias'] = $alias;
                    $i++;
                    if ($i > 30) {
                        $alias = '';
                        break;
                    }
                }
                $this->alias = $alias;
            }
            return true;
        }
        return false;
    }

    public function getUrl($scheme = false, $addParams = [])
    {
        $controller = 'list';
        if ($this->entity_type == self::ENTITY_TYPE_LIBRARY) {
            $controller = 'library';
        }
        if ($this->alias) {
            $params = [$controller . '/view', 'alias' => $this->alias];
            $params = array_merge($params, $addParams);
            return Url::to($params, $scheme);
        } else {
            $params = [$controller . '/view', 'index' => $this->id];
            $params = array_merge($params, $addParams);
            return Url::to($params, $scheme);
        }
    }


    public function addReputation($addReputation)
    {
        $user = User::thisUser();
        $modelUserId = $this->user_id;
        $paramsSelf = [
            'entity' => self::THIS_ENTITY,
            'itemId' => $this->id,
            'userId' => $user->id,
        ];
        $paramsOther = [
            'entity' => self::THIS_ENTITY,
            'itemId' => $this->id,
            'userId' => $modelUserId,
        ];

        if ($addReputation == VoteModel::ADD_REPUTATION_CANCEL_UP) {
            // - хозяину записи за отмену лайка
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM_CANCEL, $paramsSelf);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_UP) {
            // + хозяину записи за лайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_LIKE_SELF_ITEM, $paramsSelf);
            // Если раньше не было оценки, пользователь ставит лайк и его репутация маленькая, тогда добавим ему репутации
            if ($user->reputation < Item::MAX_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE &&
                $user->reputation > Item::MIN_REPUTATION_FOR_ADD_REPUTATION_ITEM_VOTE_LIKE
            ) {
                // + текущему пользователю за лайк
                Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_LIKE_OTHER_ITEM, $paramsOther);
            }
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_CANCEL_DOWN) {
            // + хозяину записи за отмену дизлайка
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM_CANCEL, $paramsSelf);
            // + текущему пользователю за отмену дизлайка
            Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM_CANCEL, $paramsOther);
        } elseif ($addReputation == VoteModel::ADD_REPUTATION_DOWN) {
            // - хозяину записи за дизлайк
            Reputation::addReputation($modelUserId, Reputation::ENTITY_VOTE_DISLIKE_SELF_ITEM, $paramsSelf);
            // - текущему пользователю за дизлайк
            Reputation::addReputation($user->id, Reputation::ENTITY_VOTE_DISLIKE_OTHER_ITEM, $paramsOther);
        }
    }

    public function extractKeywords($str, $minWordLen = 3, $minWordOccurrences = 2, $asArray = false)
    {
        $str = preg_replace('/[^\p{L}0-9 ]/u', ' ', $str);
        $str = trim(preg_replace('/\s+/u', ' ', $str));

        $words = explode(' ', $str);
        $keywords = array();
        while(($c_word = array_shift($words)) !== null) {
            if(mb_strlen($c_word) < $minWordLen) {
                continue;
            }

            $c_word = mb_strtolower($c_word);
            if(array_key_exists($c_word, $keywords)) {
                $keywords[$c_word][1]++;
            } else {
                $keywords[$c_word] = array($c_word, 1);
            }
        }
        usort($keywords, function ($first, $sec) {
            return $sec[1] - $first[1];
        });

        $final_keywords = array();
        foreach($keywords as $keyword_det) {
            if($keyword_det[1] < $minWordOccurrences) {
                break;
            }
            array_push($final_keywords, $keyword_det[0]);
        }
        return $asArray ? $final_keywords : implode(', ', $final_keywords);
    }

    public function getKeywords()
    {
        $keywords = [];
        $tags = $this->tagEntity;
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $name = $tag->tags->getName();
                if (!empty($name)) {
                    $keywords[] = $tag->tags->getName();
                }
            }
        }

        $description = strip_tags($this->description);
        $keywords = array_merge($keywords, $this->extractKeywords($this->getTitle(), 3, 1, true));
        $minWorkOk = mb_strlen($description) > 400 ? 2 : 1;
        $keywords = array_merge($keywords, $this->extractKeywords($description, 3, $minWorkOk, true));
        return $keywords;
    }
}
