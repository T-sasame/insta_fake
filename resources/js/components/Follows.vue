<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <p v-on:click="openFollow(others)" class="btn">フォロー:{{ followcount }}人</p>
                    </div>
                    <div class="col-md-4">
                        <p v-on:click="openFollower(others)" class="btn">フォロワー:{{ followercount }}人</p>
                    </div>
                    <div class="col-md-4">

                        <!-- v-showを使いフォローボタンの切り替え -->
                        <div v-show="notFollow">
                            <button class="btn btn-primary" v-on:click="follow">フォローする</button>
                        </div>
                        <div v-show="doingFollow">
                            <button class="btn btn-primary" v-on:click="deleteFollow">フォローを解除</button>
                        </div>
                    </div>

                    <!-- フォロー、フォロワー一覧をモーダルウィンドウで表示 -->
                    <followmodal v-show = "showFollow"
                                 v-on:close = "closeFollow"
                                 :follows = "follows">
                    </followmodal>

                    <followermodal v-show = "showFollower"
                                   v-on:close = "closeFollower"
                                   :followers = "followers">
                    </followermodal>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FollowModal from '../components/FollowModal.vue'; // 子コンポーネントを使用する為の定義

export default {
    name: 'Follows',
    components: {  // 子コンポーネントを使用する為の定義
        FollowModal
    },
    props: [  // 変数の受け取り
        'user',
        'others',
        'following',
        'followcount',
        'followercount'
    ],

    data() {
        return {
            follows: '',
            followers: '',
            showFollow: false,
            showFollower: false,
            doingFollow: false,
            notFollow: false
        }
    },

    methods: {
        // フォローモーダル表示のための真偽の切り替え
        openFollow: function(others) {

            // 表示するタイミングでaxios使い、followsにユーザー一覧を取得
            var params = {
                id: others.id
            };
            axios.post('/data/follows', params)
            .then(res => {
                this.follows = res.data;
            })
            .catch((error) => {
                console.log(error);
            });

            this.showFollow = true;
        },
        closeFollow: function() {
            // 非表示になったら、モーダルのコンポーネントに渡した変数を初期化
            this.follows = '';
            this.showFollow = false;
        },

        // フォロワーモーダル表示のための真偽の切り替え
        openFollower: function(others) {

            // 表示するタイミングでaxios使い、followersにユーザー一覧を取得
            var params = {
                id: others.id
            };
            axios.post('/data/followers', params)
            .then(res => {
                this.followers = res.data;
            })
            .catch((error) => {
                console.log(error);
            });

            this.showFollower = true;
        },
        closeFollower: function() {
            // 非表示になったら、モーダルのコンポーネントに渡した変数を初期化
            this.followers = '';
            this.showFollower = false;
        },

        // フォローする為のメソッド
        follow: function() {
            var params = {
                followId: this.others.id,
                followerId: this.user.id
            };
            axios.post('/user/follow', params)
            .then(res => {
                this.notFollow = false;  //v-showのフォローボタン(notとdoing)を切り替え
                this.doingFollow = true;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        // フォローを解除する為のメソッド
        deleteFollow: function() {
            //ダイアログで最終確認。返り値はtrue,false
            var res = confirm("フォローを解除してもよろしいですか？");
            if(res == true) {
                var params = {
                    followId: this.others.id,
                    followerId: this.user.id
                };
                axios.post('/user/follow/delete', params)
                .then(res => {
                    this.doingFollow = false;  //v-showのフォローボタン(notとdoing)を切り替え
                    this.notFollow = true;
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    },
    mounted() {
        if(this.user == '') {
            // userがnull(guestユーザー、mypageでの呼び出し)の場合、フォローボタンを両方非表示
            this.doingFollow = false;
            this.notFollow = false;
        } else if(this.following == 1) {
            // followingが1(フォローしていない状態)の場合、フォローするボタンを表示
            this.doingFollow = false;
            this.notFollow = true;
        } else {
            // フォローしている場合、フォローを解除ボタンを表示
            this.notFollow = false;
            this.doingFollow = true;
        }
    }
}
</script>

<style>
</style>
