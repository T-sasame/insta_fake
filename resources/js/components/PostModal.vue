<template>
    <transition name="modal" appear>
        <!-- v-on:click.self="$emit('close')"で、モーダルウィンドウ以外がクリックされたら非表示に切り替え -->
        <!-- その際、openComment,goodBtn,beforeGood,afterGoodを初期値に戻す -->
        <div id="overlay" v-on:click.self="$emit('close'), openComment = false, goodBtn = false, beforeGood = true, afterGood = false">
            <div id="content" class="container">
                <div class="row">
                    <div class="modalImage col-md-7">
                        <img :src="postItem.image">
                    </div>
                    <div class="col-md-5">
                        <a :href=" '/userpage/' + postItem.name">{{ postItem.name }}</a><hr color="#c0c0c0">
                        <p>{{ postItem.comment }}</p><hr color="#c0c0c0">
                        <div class="comment_area">

                            <!-- v-showを使い、コメント無しならnullComment、それ以外はshowCommentを表示 -->
                            <p v-show="nullComment">{{ allComments }}</p>
                            <div class="comments row" v-show="showComment" v-for="allComment in allComments" :key="allComment.id">
                                <div class="comment_icon col-md-2">
                                    <img :src="allComment.icon">
                                </div>
                                <div class="col-md-10">
                                    <p><a :href=" '/userpage/' + allComment.name">{{ allComment.name }}</a> : {{ allComment.comment }}</p>
                                </div>
                            </div>
                        </div>
                        <hr color="#c0c0c0">

                        <!-- v-ifを使い、変数userがnull(guestユーザー)か否かで表示の切り替え -->
                        <div v-if=" user != '' " class="icon_field row">
                            <!-- "openComment = !openComment"で、コメントアイコンをクリックすると、コメントフォームの表示非表示 -->
                            <img :src="imgComment" v-on:click="openComment = !openComment, goodError = false"
                                 data-toggle="tooltip" title="コメントを投稿">

                            <!-- goodBtnの真偽で、いいね！ボタンの有効無効を切り替え -->
                            <button v-bind:disabled="goodBtn" v-on:click="postGood">
                                <!-- v-show=beforeGood,afterGoodでいいね！ボタンを押す前と後での画像の切り替え -->
                                <img v-show="beforeGood" :src="imgGood"
                                     data-toggle="tooltip" title="いいね！をする">
                                <img v-show="afterGood" :src="imgGoodAfter"
                                     data-toggle="tooltip" title="いいね！を登録済みです">
                            </button>
                            <p style="margin:auto 0px">{{ countGood }}いいね！</p>
                        </div>

                        <!-- guestユーザーなのでコメントやいいね！を行えないようにする -->
                        <div v-else class="icon_field row">
                            <img :src="imgComment" data-toggle="tooltip" title="コメントを投稿するにはログインが必要です">

                            <img :src="imgGood" data-toggle="tooltip" title="いいね！をするにはログインが必要です">
                            <p style="margin:auto 0px">{{ countGood }}いいね！</p>
                        </div>

                        <small v-show="goodError" class="form-text text-info">既にいいね！は登録済みです</small>
                        <div v-show="openComment">
                            <textarea v-model="comment" class="form-control" rows="2" placeholder="コメント入力"></textarea>
                            <small v-show="errorMessage" class="form-text text-info">コメントは255文字以内で入力して下さい</small>
                            <button class="btn btn-primary" v-bind:disabled="commentBtn" v-on:click="postComment">投稿</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'PostModal',
    props: [  // 変数の受け取り
        'postItem',
        'commentAll',
        'goodCount',
        'user'
    ],
    data() {
        return {
            allComments: '',
            countGood: '',
            goodError: false,
            comment: '',
            showComment: false,
            nullComment: false,
            errorMessage: true,
            openComment: false,
            commentBtn: true,
            goodBtn: false,
            // コメント・いいね！アイコンのURLを変数に格納
            imgGood: 'https://s3-ap-northeast-1.amazonaws.com/sasame.images/good.png',
            imgGoodAfter: 'https://s3-ap-northeast-1.amazonaws.com/sasame.images/goodafter.png',
            imgComment: 'https://s3-ap-northeast-1.amazonaws.com/sasame.images/comment.png',
            beforeGood: true,
            afterGood: false
        }
    },
    methods: {
        // コメントを投稿するメソッド
        postComment() {
            var params = {
                postId: this.postItem.id,
                comment: this.comment
            };
            axios.post('/user/comment', params)
            .then(res => {
                this.comment = '';  // 投稿に成功したコメント変数の初期化
                this.openComment = false;  //コメントフォームを非表示

                // 投稿に成功したら、再度コメントを取得
                var params = {
                    id: this.postItem.id
                };
                axios.post('/data/comments', params)
                .then(res => {
                    this.allComments = res.data;
                    this.nullComment = false;  //v-showのコメントフィールド(nullとshow)を切り替え
                    this.showComment = true;
                });
            })
            .catch((error) => {
                console.log(error);
            });
        },
        // いいね！を登録するメソッド
        postGood() {
            var params = {
                postId: this.postItem.id,
                userId: this.user
            };
            axios.post('/user/good', params)
            .then(res => {
                // いいね！の登録に成功したら、いいね！の総数を+1
                this.countGood += 1;
                this.goodBtn = true;  // v-bind:disabled="goodBtn"をtrueにして、ボタンの機能停止
                this.beforeGood = false;  //v-showのいいね！ボタン画像(beforeとafter)を切り替え
                this.afterGood = true;
            })
            .catch((error) => {
                this.goodError = true;  //v-show="goodError"でエラーメッセージの表示
                this.goodBtn = true;
                this.beforeGood = false;
                this.afterGood = true;
                // console.log(error);
            });
        }
    },
    watch: {
        // watchを使い、propsで渡ってきたデータ"commentAll"の変更を監視する。値は"allComments"に格納
        'commentAll': function() {
            if(this.commentAll == '') {
                this.nullComment = true;  //v-showのコメントフィールド(nullとshow)を切り替え
                this.showComment = false;
                this.allComments = 'コメントはまだありません';
            } else {
                this.nullComment = false;
                this.showComment = true;
                this.allComments = this.commentAll;
            }
        },
        // propsのデータ"goodCount"の変更を監視。いいね！の総数は"countGood"に格納
        'goodCount': function() {
            this.countGood = this.goodCount;
            this.goodError = false;  //v-show="goodError"の初期化
        },
        // コメントフォームの入力値を監視して、フロント側でのバリデーションを行う
        'comment': function() {
            // コメントが空っぽ、又は最大文字数の255を超えた場合、エラーメッセージを表示し送信ボタンの機能停止
            if(this.comment == '' || this.comment.length > 255) {
                this.errorMessage = true;
                this.commentBtn = true;
            } else {
              this.errorMessage = false;
              this.commentBtn = false;
            }
        }
    },
    mounted() {
        // ツールチップの表示
        $('[data-toggle = "tooltip"]').tooltip();
    }
}
</script>

<style>
#content{
  z-index:10;
  width:70%;
  max-height: 95%;
  padding: 1em;
  background:#fff;
  position:fixed;
  overflow: hidden;
  overflow-y: auto;
}

#overlay{
  z-index: 1;  /* 要素を重ねた時の順番 */

  /* 画面全体を覆う設定 */
  position:fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4);

  /* 画面の中央に要素を表示させる設定 */
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.4s;
}
.modal-enter, .modal-leave {
    opacity: 0;
}
</style>
