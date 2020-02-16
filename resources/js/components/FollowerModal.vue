<template>
    <transition name="modal" appear>
        <!-- v-on:click.self="$emit('close')"で、モーダルウィンドウ以外がクリックされたら非表示に切り替え -->
        <div id="overlay" v-on:click.self="$emit('close')">
            <div id="contentFollower" class="container">
                <p>フォロワー</p><hr color="#c0c0c0">
                <div class="col-md-12" v-for="follower in followers" :key="follower.id">
                    <div class="row">
                        <div class="follow_icon col-md-4">
                            <img :src="follower.icon">
                        </div>
                        <div class="col-md-8">
                            <p><a :href=" '/userpage/' + follower.name">{{ follower.name }}</a></p>
                        </div>
                    </div>
                    <hr color="#c0c0c0">
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'FollowerModal',
    props: [  // 変数の受け取り
        'followers'
    ],
    data() {
        return {
        }
    }
}
</script>

<style>
#contentFollower{
  z-index:10;
  width:30%;
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
