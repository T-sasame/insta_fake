<template>
    <div class="container">
        <div class="row">
            <div class="postsUserpage col-md-12">
                <div class="row">
                    <div class="col-md-4" v-for="post in childPosts.data" :key="post.id">
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
                                       :user = "user.id"
                                       :page = "page">
                            </postmodal>
                        </div>
                        <div v-else>
                            <!-- guestユーザーなので、:userにnullを格納して渡す -->
                            <postmodal v-show = "showContent"
                                       v-on:close = "closeModal"
                                       :postItem = "postItem"
                                       :commentAll = "commentAll"
                                       :goodCount = "goodCount"
                                       :user = " '' "
                                       :page = "page">
                            </postmodal>
                        </div>
                    </div>
                    <!-- vue-infinite-loadingを使い、laravelのpaginationで指定された件数毎に読み込む -->
                    <infinite-loading @infinite="infinite" :distance=0></infinite-loading>
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
        'posts',
        'others'
    ],

    data() {
        return {
            childPosts: '',
            postItem: '',
            commentAll: '',
            goodCount: '',
            showContent: false,
            page: '',
            postsPage: 2
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
            axios.post('/data/comments?page=1', params)
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
            // paginateのページ数を判断する為、this.page = 1をする
            this.postItem = post;
            this.page = 1;
            this.showContent = true;
        },
        closeModal: function() {
            // 非表示になったら、モーダルのコンポーネントに渡した変数を初期化
            this.commentAll = '';
            this.postItem = '';
            this.goodCount = '';
            this.showContent = false;
            this.page = '';
        },
        // 要素の最下部までスクロールした際の、infinite-loadingの処理
        infinite($state) {
            var params = {
                id: this.others,
            };
            axios.post('/data/posts?page=' + this.postsPage, params)
            .then(res => {
                if(this.postsPage <= res.data.last_page) {
                    this.postsPage += 1;
                    this.childPosts.data.push(...res.data.data);
                    $state.loaded();  //読み込みの継続
                } else {
                    $state.complete();  //読み込みの終了
                }
            })
            .catch((error) => {
                $state.complete();
            });
        }
    },
    mounted: function() {
        this.childPosts = this.posts;
    }
}
</script>

<style>
</style>
