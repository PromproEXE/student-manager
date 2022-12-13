<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
</script>
<script>
export default {
    data() {
        return {

        }
    },
    methods: {
        async getGrade() {
            let bodyFormData = new FormData();
            bodyFormData.append('userName', 'Fred');
            let data = {
                TxtUser: '09362',
                txtPassword: '27/02/2548',
                __VIEWSTATEGENERATOR: 'BD62C3F6',
                __VIEWSTATE: '6wuxjRuG/hSbU70cDzg1nZ960MCt5kXibrh/0Hs6z+P6cI7h8S7n1TNstPMuJsRNhtUOq/MlQJ80DUbKabGL+nQP3cj1+BZGRnx3IFuV5r8cOx3ihuvLxBpgIrCc3fjgQfANMOw9kHSl2HJAtfVnv+hogQn2zs2rJi3QMNlY7zW8IitKNM5RIcSfmj6tsuABGshYD8bwloD16+J3rA3eOm0HL929sqR0+4SY9pJJtFkG15h3td6WSWxbr1bpn//x11/drxY3+vrqlByr6x37C4/nKtJPJ0UUBtncwAnT6xZ89wPgO5DnLzrc/uOeUn49MoWughJda+1nQooWwflUuOr9Vs8tvGQSazoM/zZvhVU8C/WRPQtX/NGRHC0pDqesXU+ksNba17y/nFjs6L2+Mpnp7R1jEoT/+8/WRSVzD23ls+hWdv/VynHGrDIgy4GpxiSwqUsyGOg1sRpPblsjFPPpan4v1alFefVaFjVNdTVuZwEkDe6Lt/7PxqVy1IduCXJRH9SoMYf/QsW1Y2D1KdVMQ7/BcXky4ydLmOmvYWQ/z36rosZ6UjphQw9gx2uadgnbOqEjyRc/tfJevlJHcRru968XVHRhw4SsEoOKjaGVQuyLkNLcRYQuAoljWeXO9V01kEBONbVQzdQG54HqDF0Y+oe5guMpfNbYEhxEttg/5s8pQ6b/9Z19b+sEDChQDu7CaAu2oOLXOJS7K4FyjzvPnRtssSXzV36tw8RShiFQv7XrYSwWUP2ivTkbL2qxwKR9J9HwO3Z+bCj5G0Z8fjgXfLVNmlEBRHTyIlFBl58Qt3E0pbs8+FvvEHCByO+rjdLaOVGqgkSIsA6li6CTJF5SwLIrHXm2EztTQux05fCoQGSlyG2Ih9JZQDp7v8vbYDNFvbjxpT0uWf2CfCBUPn6b5TVdHlFWsLBk5fohw7uOUkGBtNZGxqawL1RhD7YWBiGdI+2O1IW1h9CdAyA0n7Dr2RLZF7GFk9RYQ+YQtlEtBydre1HtMQkx0HISKrENpmXEgrmXdzIy96TfXIKS3l5rp8BBA9Ytyki2Ymfwcqlzo8V0YnMygg6N1K0w+OS9MHiNBVRqzE8ESw5XgRmJPb8crIiukOVUnwTKaj4PyMyY5Gd40/F5c5esc7oUjxrwXFKqxBNfyMIjt+fXj3qpuzoWkUzBY+y9Gaf4BEWxRDDUPDK3etfnhXgfaT8+vjengeCAK7DxfIZg1nLYbJV8NY+mKo3ASgPdieQqKpqJUcO9BBjuBCCEwH1mP7aHdNaFfNQQFU6jTwq030J/3+YDiTq+B8USLbuiyINn/8QUrgf+SQmrkdGXnOz2r6PIjoGbHZCxw45F93ATixMlJW5dlXriSm24bBhzQ1jvlgKLQYk=',
                __EVENTVALIDATION: 'Z2qj0u5KB4T3s9njwpgM+UoIqP0Uq5MpMQlYM50Ok3aUW5eGGOEtNQVcggSyj4VgZAdcURwPWLscu5iR7KYoFEenWwC7qFd5d7EbPrQE9OpBJO5wtnQfyELVA/aV64FoW3fqge32mPvwNbvqRGiWz+nr9xItIJ9dSh0Gumf/AB0J+yAOjauk48e0QsC2LD5dX6ni2IR3/x7mhrI1w/KaqTzozaGm6I7s28pLtrKzmTknXFWdAvxCH2JT/eK5FVT5+ndNqTYg+PcwZ/3+NR5hZhIVz+ouZgCyKhBvyvcFDUQ5EdYdfNGK6RA9mMcVvVbv7mIeKYS1pvfr8Our1esKf+U/k386CC4KOlM5eZY4/joR52Xa0rzbBMcTTjq6DIfXLbu3Om0vVpUYXS1uEWTr690uksDhicCjSthrvs8Kl1c=',
                ButtonX1: ''
            }
            let res = await axios({
                url: 'http://dograde.online/kpn/default.aspx',
                method: 'post',
                data: bodyFormData,
                headers: { "Content-Type": "multipart/form-data" },
            })
            console.log(res.data)
        }
    },
    mounted() {
        this.getGrade()
        // document.getElementById("myAnchor").addEventListener("click", function (event) {
        //     event.preventDefault()
        // });
    }
}
</script>
<template>

    <Head title="ดูเกรด"></Head>
    <AppLayout>
        <template #header>ดูเกรด</template>
        <div class="rounded-xl bg-white p-5" style="height: calc(100vh - 140px)">
            <form class="hidden" action="http://dograde.online/kpn/default.aspx" method="POST" target="grade_iframe"
                id="myAnchor">
                <input type="text" name="TxtUser" id="TxtUser" value="09362">
                <input type="text" name="txtPassword" id="txtPassword" value="27/02/2548">
                <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="BD62C3F6">
                <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE"
                    value="6wuxjRuG/hSbU70cDzg1nZ960MCt5kXibrh/0Hs6z+P6cI7h8S7n1TNstPMuJsRNhtUOq/MlQJ80DUbKabGL+nQP3cj1+BZGRnx3IFuV5r8cOx3ihuvLxBpgIrCc3fjgQfANMOw9kHSl2HJAtfVnv+hogQn2zs2rJi3QMNlY7zW8IitKNM5RIcSfmj6tsuABGshYD8bwloD16+J3rA3eOm0HL929sqR0+4SY9pJJtFkG15h3td6WSWxbr1bpn//x11/drxY3+vrqlByr6x37C4/nKtJPJ0UUBtncwAnT6xZ89wPgO5DnLzrc/uOeUn49MoWughJda+1nQooWwflUuOr9Vs8tvGQSazoM/zZvhVU8C/WRPQtX/NGRHC0pDqesXU+ksNba17y/nFjs6L2+Mpnp7R1jEoT/+8/WRSVzD23ls+hWdv/VynHGrDIgy4GpxiSwqUsyGOg1sRpPblsjFPPpan4v1alFefVaFjVNdTVuZwEkDe6Lt/7PxqVy1IduCXJRH9SoMYf/QsW1Y2D1KdVMQ7/BcXky4ydLmOmvYWQ/z36rosZ6UjphQw9gx2uadgnbOqEjyRc/tfJevlJHcRru968XVHRhw4SsEoOKjaGVQuyLkNLcRYQuAoljWeXO9V01kEBONbVQzdQG54HqDF0Y+oe5guMpfNbYEhxEttg/5s8pQ6b/9Z19b+sEDChQDu7CaAu2oOLXOJS7K4FyjzvPnRtssSXzV36tw8RShiFQv7XrYSwWUP2ivTkbL2qxwKR9J9HwO3Z+bCj5G0Z8fjgXfLVNmlEBRHTyIlFBl58Qt3E0pbs8+FvvEHCByO+rjdLaOVGqgkSIsA6li6CTJF5SwLIrHXm2EztTQux05fCoQGSlyG2Ih9JZQDp7v8vbYDNFvbjxpT0uWf2CfCBUPn6b5TVdHlFWsLBk5fohw7uOUkGBtNZGxqawL1RhD7YWBiGdI+2O1IW1h9CdAyA0n7Dr2RLZF7GFk9RYQ+YQtlEtBydre1HtMQkx0HISKrENpmXEgrmXdzIy96TfXIKS3l5rp8BBA9Ytyki2Ymfwcqlzo8V0YnMygg6N1K0w+OS9MHiNBVRqzE8ESw5XgRmJPb8crIiukOVUnwTKaj4PyMyY5Gd40/F5c5esc7oUjxrwXFKqxBNfyMIjt+fXj3qpuzoWkUzBY+y9Gaf4BEWxRDDUPDK3etfnhXgfaT8+vjengeCAK7DxfIZg1nLYbJV8NY+mKo3ASgPdieQqKpqJUcO9BBjuBCCEwH1mP7aHdNaFfNQQFU6jTwq030J/3+YDiTq+B8USLbuiyINn/8QUrgf+SQmrkdGXnOz2r6PIjoGbHZCxw45F93ATixMlJW5dlXriSm24bBhzQ1jvlgKLQYk=">
                <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION"
                    value="Z2qj0u5KB4T3s9njwpgM+UoIqP0Uq5MpMQlYM50Ok3aUW5eGGOEtNQVcggSyj4VgZAdcURwPWLscu5iR7KYoFEenWwC7qFd5d7EbPrQE9OpBJO5wtnQfyELVA/aV64FoW3fqge32mPvwNbvqRGiWz+nr9xItIJ9dSh0Gumf/AB0J+yAOjauk48e0QsC2LD5dX6ni2IR3/x7mhrI1w/KaqTzozaGm6I7s28pLtrKzmTknXFWdAvxCH2JT/eK5FVT5+ndNqTYg+PcwZ/3+NR5hZhIVz+ouZgCyKhBvyvcFDUQ5EdYdfNGK6RA9mMcVvVbv7mIeKYS1pvfr8Our1esKf+U/k386CC4KOlM5eZY4/joR52Xa0rzbBMcTTjq6DIfXLbu3Om0vVpUYXS1uEWTr690uksDhicCjSthrvs8Kl1c=">
                <input type="hidden" name="ButtonX1" value="">

                <button type="submit">submit</button>
            </form>
            <iframe name="grade_iframe" src="http://dograde.online/kpn/default.aspx" style="height: calc(100vh - 225px)"
                class="w-full" frameborder="0"></iframe>
        </div>
    </AppLayout>
</template>
