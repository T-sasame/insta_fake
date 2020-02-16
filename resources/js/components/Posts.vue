<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4" v-for="post in posts" :key="post.id">
                        <div class="image">
                            <!-- 画像をクリックするとモーダルウィンドウがポップアップ -->
                            <img :src="post.image" v-on:click="openModal(post)">
                        </div>

                        <!-- v-ifを使い、変数userがnull(guestユーザー)か否かで表示の切り替え -->
                        <div v-if=" user != '' ">
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
                        <div v-else>
                            <!-- guestユーザーなので、:userにnullを格納して渡す -->
                            <postmodal v-show = "showContent"
                                       v-on:close = "closeModal"
                                       :postItem = "postItem"
                                       :commentAll = "commentAll"
                                       :goodCount = "goodCount"
                                       :user = " '' ">
                            </postmodal>
                        </div>
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
        }
    }
}
</script>

<style>
</style>
