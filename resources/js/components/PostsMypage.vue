<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4" v-for="(post,index) in childPosts" :key="post.id">
                        <div class="image">
                            <!-- 画像をクリックするとモーダルウィンドウがポップアップ -->
                            <img :src="post.image" v-on:click="openModal(post)">
                        </div>
                        <div class="row justify-content-center">
                            <div class="edit">
                                <a :href=" '/user/post/edit/' + post.id" class="btn btn-primary">編集</a>
                            </div>
                            <div class="edit">
                                <button class="btn btn-danger" v-on:click="postDelete(post, index)">削除</button>
                            </div>
                        </div>
                        <hr color="#c0c0c0">
                        <!-- v-show = "showContent"の真偽で表示させる -->
                        <!-- モーダルのコンポーネントからcloseが$emitされたら、closeModalの処理でモーダルを閉じる -->
                        <!-- モーダルウィンドウのコンポーネントに" :変数名 = "渡すデータ" "で画像毎のデータを渡す -->
                        <postmodal v-show = "showContent"
                                   v-on:close = "closeModal"
                                   :postItem = "postItem"
                                   :commentAll = "commentAll"
                                   :goodCount = "goodCount"
                                   :user = "user.id">
                        </postmodal>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PostModal from '../components/PostModal.vue'; // 子コンポーネントを使用する為の定義

export default {
    name: 'Posts',
    components: {  // 子コンポーネントを使用する為の定義
        PostModal
    },
    props: [  // 変数の受け取り
        'user',
        'posts'
    ],

    data() {
        return {
            childPosts: '',
            postItem: '',
            commentAll: '',
            goodCount: '',
            showContent: false
        }
    },

    methods: {
        // モーダルウィンドウ表示のための真偽の切り替え
        // 引数のpostはv-forで回している画像毎のデータ
        openModal: function(post) {

            // 表示するタイミングでaxios使い、commentAllに画像へのコメントを取得
            var params = {
                id: post.id
            };
            axios.post('/data/comments', params)
            .then(res => {
                this.commentAll = res.data;

                // コメント取得に成功したら、次はいいね！の総数を取得
                var params = {
                    id: post.id
                };
                axios.post('/data/goods', params)
                .then(res => {
                    this.goodCount = res.data;
                });
            })
            .catch((error) => {
                console.log(error);
            });
            // 表示する際は画像毎のデータをpostItemに格納
            this.postItem = post;
            this.showContent = true;
        },
        closeModal: function() {
            // 非表示になったら、モーダルのコンポーネントに渡した変数を初期化
            this.commentAll = '';
            this.postItem = '';
            this.goodCount = '';
            this.showContent = false;
        },
        //投稿画像を削除するメソッド
        postDelete(post, index) {
            //ダイアログで最終確認。返り値はtrue,false
            var res = confirm("本当に削除してもよろしいですか？");
            if(res == true) {
                var params = {
                    id: post.id
                };
                axios.post('/user/post/delete', params)
                .then(res => {
                    //spliceで配列から削除。index番目の値を1つ削除
                    this.childPosts.splice(index, 1);
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    },
    mounted: function() {
        this.childPosts = this.posts;
    }
}
</script>

<style>
</style>
