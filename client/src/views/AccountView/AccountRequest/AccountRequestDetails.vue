<template>
  <BaseFlatDialog
    :open="dialog.show"
    :title="dialog.title"
    :mode="dialog.mode"
    @close="dialog.show = false"
  >
    <template #default v-if="!isLoading">
      {{ dialog.message }}
    </template>
    <template #action> <div v-if="isLoading"></div> </template>
  </BaseFlatDialog>
  <div class="main">
    <h1>Request Details</h1>
    <template v-if="!isLoading">
      <template v-if="data">
        <table>
          <tr class="request-row">
            <th colspan="2">{{ data.type.toLocaleUpperCase() }}</th>
          </tr>
          <tr class="request-row">
            <th colspan="2" class="small">Name</th>
          </tr>
          <tr class="request-row">
            <th class="medium">Name</th>
            <td colspan="2">{{ data.body.name }}</td>
          </tr>
          <tr class="request-row">
            <th colspan="2" class="small">Description</th>
          </tr>
          <tr class="request-row">
            <th class="medium">Descriptions</th>
            <td colspan="2">{{ data.body.description }}</td>
          </tr>
          <tr class="request-row">
            <th colspan="2" class="small">Requested by</th>
          </tr>
          <tr class="request-row">
            <th class="medium">Requested by</th>
            <td colspan="2">{{ data.requester.name }}</td>
          </tr>
          <tr class="request-row">
            <th colspan="2" class="small">Requested at</th>
          </tr>
          <tr class="request-row">
            <th class="medium">Requested at</th>
            <td colspan="2">
              {{ new Date(data.created_at).toLocaleString() }}
            </td>
          </tr>
          <tr class="request-row">
            <th colspan="2" class="small">Filled by</th>
          </tr>
          <tr class="request-row">
            <th class="medium">Filled by</th>
            <td colspan="2">
              {{
                data.filled_by instanceof Object ? data.filled_by.name : "No"
              }}
            </td>
          </tr>
          <tr v-if="
              data.user_fillable &&
              !data.filled_by &&
              data.responses.findIndex(
                (r) => r.responder.user_id === user.user_id
              ) === -1
            "
            class="request-row"
          >
            <th colspan="2" class="small">Response Request</th>
          </tr>
          <tr
            class="request-row"
            v-if="
              data.user_fillable &&
              !data.filled_by &&
              data.responses.findIndex(
                (r) => r.responder.user_id === user.user_id
              ) === -1
            "
          >
            <th class="medium">Response Request</th>
            <td colspan="2">
              <div class="response-content">
                <textarea
                  name="response-content"
                  id="response-content"
                  rows="10"
                  placeholder="Write your response here..."
                  v-model="responseContent"
                ></textarea>
                <div class="note">
                  <p>You can fill this form only once.</p>
                  <p>The response must be url or details about the request.</p>
                  <p>Please take responsibility for the response.</p>
                </div>
                <button class="btn response" @click="onResponse">
                  Response
                </button>
              </div>
            </td>
          </tr>
        </table>
        <table>
          <thead>
            <tr>
              <th colspan="5">Responses</th>
            </tr>
            <tr>
              <th class="index">#</th>
              <th class="content">Content</th>
              <th class="responded">Responded</th>
              <th class="status">Status</th>
              <th
                v-if="
                  data.requester.user_id === user.user_id && !data.filled_by
                "
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              class="response-row"
              v-for="(response, index) in data.responses"
              :key="index"
            >
              <td class="index">{{ index + 1 }}</td>
              <td class="content">
                <p>
                  {{ response.content }}
                </p>
                <span>
                  Responded at
                  {{ new Date(response.created_at).toLocaleString() }}
                  <br />
                  by: {{ response.responder.name }}
                </span>
              </td>
              <td class="responded" width="25%">
                Responded at
                {{ new Date(response.created_at).toLocaleString() }}
                <br />
                by: {{ response.responder.name }}
              </td>
              <td class="status">
                {{ response.status.toUpperCase() }}
              </td>
              <td
                class="control"
                v-if="
                  data.requester.user_id === user.user_id && !data.filled_by
                "
              >
                <template v-if="response.status === 'responded'">
                  <ConfirmFlatDialogVue
                    :passingData="response.id"
                    @confirm="onApprove"
                  >
                    <template #message>
                      <p>
                        Are you sure you want to approve this response? This
                        action will make all otther responses rejected.
                      </p>
                    </template>
                    <button class="btn approve">Approve</button>
                  </ConfirmFlatDialogVue>
                  <ConfirmFlatDialogVue
                    :passingData="response.id"
                    @confirm="onReject"
                  >
                    <template #message>
                      <p>
                        Are you sure you want to reject this response? This is
                        irreversible.
                      </p>
                    </template>
                    <button class="btn reject">Reject</button>
                  </ConfirmFlatDialogVue>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </template>
      <span v-else>Request not found</span>
    </template>
    <template v-else>
      <BaseCircleLoad />
    </template>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import type { request } from "@/model/requestModel";
import type { user } from "@/model/userModel";
import type { response } from "@/model/responseModel";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import ConfirmFlatDialogVue from "@/components/AccountView/partials/Dialog/ConfirmFlatDialog.vue";

type data = request & {
  requester: user;
  responses: (response & { responder: user })[];
};
export default defineComponent({
  data() {
    return {
      isLoading: false,
      data: null as data | null,
      responseContent: "",
      dialog: {
        show: false,
        title: "",
        mode: "warning", // "warning", "error", "success", "info
        message: "",
      },
    };
  },
  methods: {
    ...mapActions("request", ["show", "response", "approve", "reject"]),
    loadData() {
      this.isLoading = true;
      this.show({ token: this.token, id: this.$route.params.id })
        .then((res) => {
          return res.json();
        })
        .then((data: { request: data; status: string }) => {
          this.isLoading = false;
          if (data.status === "success") {
            if (data.request.status !== "pending") {
              data.request.responses = data.request.responses.filter(
                (response) => response.status === "approved"
              );
            }
            this.data = data.request;
          }
        })
        .catch((err) => {
          this.isLoading = false;
        });
    },
    onResponse() {
      this.isLoading = true;
      if (this.responseContent.length === 0) {
        this.showErrorMessage("Response content cannot be empty");
        return;
      }
      this.response({
        token: this.token,
        id: this.data?.id ?? this.$route.params.id,
        content: this.responseContent,
      })
        .then((res) => {
          return res.json();
        })
        .then((data: { status: string }) => {
          this.isLoading = false;
          this.loadData();
          this.responseContent = "";
        })
        .catch((err) => {
          this.isLoading = false;
        });
    },
    onApprove(id: string) {
      this.isLoading = true;
      this.approve({
        token: this.token,
        response_id: id,
        request_id: this.data?.id ?? this.$route.params.id,
      })
        .then((res) => res.json())
        .then((data: any) => {
          if (data.status === "success") {
            this.loadData();
            this.showSuccessMessage(data.message);
          } else {
            this.isLoading = false;
            this.showErrorMessage(data.message);
          }
        })
        .catch((err) => {
          this.isLoading = false;
          this.showErrorMessage(err.message);
        });
    },
    onReject(id: string) {
      this.isLoading = true;
      this.reject({
        token: this.token,
        response_id: id,
        request_id: this.data?.id ?? this.$route.params.id,
      })
        .then((res) => res.json())
        .then((data: any) => {
          if (data.status === "success") {
            this.loadData();
            this.showSuccessMessage(data.message);
          } else {
            this.isLoading = false;
            this.showErrorMessage(data.message);
          }
        })
        .catch((err) => {
          this.isLoading = false;
          this.showErrorMessage(err.message);
        });
    },
    showErrorMessage(message: string) {
      this.isLoading = false;
      this.dialog.show = true;
      this.dialog.title = "Error";
      this.dialog.mode = "error";
      this.dialog.message = message;
    },
    showSuccessMessage(message: string) {
      this.isLoading = false;
      this.dialog.show = true;
      this.dialog.title = "Success";
      this.dialog.mode = "success";
      this.dialog.message = message;
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      user: "auth/userData",
    }),
  },
  mounted() {
    this.loadData();
  },
  components: { BaseCircleLoad, BaseFlatDialog, ConfirmFlatDialogVue },
});
</script>

<style lang="scss" scoped>
.main {
  width: 100%;
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: auto;
  .btn {
    padding: 5px 10px;
    background: transparent;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    margin: 5px 2px;
    &.response {
      border: 1px solid var(--color-primary);
      color: var(--color-primary);
      &:focus {
        outline: none;
      }
      &:hover {
        background: var(--color-primary);
        color: var(--background-color-secondary);
      }
    }
    &.approve {
      border: 1px solid var(--color-primary);
      color: var(--color-primary);
      &:focus {
        outline: none;
      }
      &:hover {
        background: var(--color-primary);
        color: var(--background-color-secondary);
      }
    }
    &.reject {
      border: 1px solid var(--color-danger);
      color: var(--color-danger);
      &:focus {
        outline: none;
      }
      &:hover {
        background: var(--color-danger);
        color: var(--background-color-secondary);
      }
    }
  }
  table {
    tr {
      th {
        padding: 5px;
        font-weight: 900;
        font-size: 1.2rem;
        background: var(--background-glass-color-primary);
        text-align: center;
        &.responded {
          @media screen and (max-width: 768px) {
            display: none;
          }
        }
      }
      td {
        padding: 5px;
        font-size: 1.2rem;
        border-bottom: 1px solid var(--background-glass-color-primary);
        height: 40px;
        word-break: break-word;
        & .response-content {
          textarea {
            width: calc(100% - 30px);
            resize: none;
            padding: 10px;
            outline: none;
            border: 1px solid var(--color-primary);
            background: var(--background-color-secondary);
            color: var(--text-primary-color);
            font-size: 0.9rem;
            font-weight: 500;
          }
          .note {
            padding: 10px;
            font-size: 0.8rem;
            color: var(--color-danger);
            p {
              margin: 0;
              padding: 0;
              text-align: justify;
            }
          }
        }
      }
      &.request-row {
        th {
          &.small {
            display: none;
            @media screen and (max-width: 768px) {
              display: table-cell;
            }
          }
          &.medium {
            display: table-cell;
            @media screen and (max-width: 768px) {
              display: none;
            }
          }
        }
        td {
          @media screen and (max-width: 768px) {
            column-span: all;
          }
        }
      }
      &.response-row {
        td {
          &.index {
            min-width: 25px;
            text-align: center;
          }
          &.control {
            text-align: end;
            max-width: 200px;
          }
          &.responded {
            font-size: 1rem;
            @media screen and (max-width: 768px) {
              display: none;
            }
          }
          &.status {
            text-align: center;
            font-weight: 900;
            min-width: 100px;
            max-width: 100px;
          }
          &.control {
            min-width: 90px;
          }
          &.content {
            p {
              margin: 10px 0;
              word-break: break-word;
            }
            span {
              display: none;
              font-size: 0.9rem;
              @media screen and (max-width: 768px) {
                display: block;
              }
            }
          }
        }
      }
    }
  }
}
</style>
