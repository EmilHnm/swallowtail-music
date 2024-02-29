<template>
  <div class="main">
    <div class="heading">
      <h1>Request Management</h1>
      <router-link :to="{ name: 'accountRequestCreate' }" class="btn create"
        >Create new request</router-link
      >
    </div>
    <div class="filter">
      <div class="filter-item">
        <span>Filter with:</span>
      </div>
      <div class="filter-item">
        <input
          type="text"
          placeholder="Start typing to search..."
          v-model="filter.query"
        />
      </div>
      <div class="filter-item">
        <select v-model="filter.type">
          <option value="">Select Type</option>
          <option value="artist">Artist</option>
          <option value="genre">Genre</option>
          <option value="album">Album</option>
          <option value="song">Song</option>
        </select>
        <select v-model="filter.status">
          <option value="">Select Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <select v-model="filter.my_requests">
          <option value="false">All Requests</option>
          <option value="true">My Requests</option>
        </select>
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <td class="index">#</td>
          <td class="title">Title</td>
          <td class="type">Type</td>
          <td class="status">Status</td>
          <td class="created-at">Created At</td>
          <td class="control">Control</td>
        </tr>
      </thead>
      <tbody v-if="requests.length == 0">
        <tr v-if="isLoading">
          <td colspan="6">
            <BaseCircleLoadVue />
          </td>
        </tr>
        <tr v-else>
          <td colspan="6">No data</td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="(request, index) in requests" :key="request.id">
          <td class="index">{{ index + 1 + paginate.currentPage * 10 }}</td>
          <td class="title">
            <BaseTooltip :tooltip-text="request.body.name" :position="'bottom'">
              {{ request.body.name }}
            </BaseTooltip>
          </td>
          <td class="type">{{ request.type }}</td>
          <td class="status">{{ request.status }}</td>
          <td class="created-at">
            <BaseTooltip
              :tooltip-text="new Date(request.created_at).toLocaleString()"
              :position="'bottom'"
            >
              {{ dateService.dateDiffForHuman(request.created_at) }}
            </BaseTooltip>
          </td>
          <td class="control">
            <button
              class="btn show"
              @click="
                $router.push({
                  name: 'accountRequestDetails',
                  params: { id: request.id },
                })
              "
            >
              Details
            </button>
            <ConfirmFlatDialog
              :passingData="request.id"
              @confirm="onCancelRequest"
            >
              <template #message>
                <span>Are you sure you want to cancel this request?</span>
              </template>
              <button
                class="btn cancel"
                v-if="
                  request.status == 'pending' &&
                  request.requester == userData.user_id
                "
              >
                Cancel
              </button>
            </ConfirmFlatDialog>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination" v-if="paginate.totalPages > 1">
      <BaseTableBodyPaginationVue
        :totalPages="paginate.totalPages"
        :currentPage="paginate.currentPage"
        @onClickPage="onClickPage"
        @onGoToFirstPage="onGoToFirstPage"
        @onGoToPreviousPage="onGoToPreviousPage"
        @onGoToNextPage="onGoToNextPage"
        @onGoToLastPage="onGoToLastPage"
      />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type { request, RequestResponse } from "@/model/requestModel";
import BaseTableBodyPaginationVue from "@/components/UI/BaseTableBodyPagination.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import { mapActions, mapGetters } from "vuex";
import { DateService } from "@/mixins/DateService";
import BaseCircleLoadVue from "@/components/UI/BaseCircleLoad.vue";
import BaseTooltip from "@/components/UI/BaseTooltip.vue";
import ConfirmFlatDialog from "@/components/AccountView/partials/Dialog/ConfirmFlatDialog.vue";
export default defineComponent({
  components: {
    BaseTableBodyPaginationVue,
    BaseLineLoad,
    BaseCircleLoadVue,
    BaseTooltip,
    ConfirmFlatDialog,
  },
  computed: {
    ...mapGetters({
      userToken: "auth/userToken",
      userData: "auth/userData",
    }),
  },
  data() {
    return {
      requests: [] as request[],
      paginate: {
        currentPage: 0,
        totalPages: 0,
      },
      filter: {
        type: "" as "artist" | "genre" | "album" | "song" | "",
        status: "" as "pending" | "approved" | "denied" | "",
        my_requests: false,
        query: "",
      },
      isLoading: false,
      dateService: new DateService(),
      controller: new AbortController(),
      signal: null as AbortSignal | null,
    };
  },
  methods: {
    ...mapActions("request", ["index", "cancel"]),
    getRequests() {
      this.isLoading = true;
      if (!this.signal) this.signal = this.controller.signal;
      else {
        this.controller.abort();
        this.controller = new AbortController();
        this.signal = this.controller.signal;
      }

      this.index({
        token: this.userToken,
        type: this.filter.type,
        page: this.paginate.currentPage + 1,
        status: this.filter.status,
        my_requests: this.filter.my_requests,
        signal: this.signal,
        query: this.filter.query,
      })
        .then((res: any) => res.json())
        .then((data: { requests: RequestResponse; status: string }) => {
          this.requests = data.requests.data;
          this.paginate.currentPage = data.requests.current_page - 1;
          this.paginate.totalPages = data.requests.last_page;
          this.isLoading = false;
        })
        .catch((err) => {
          if (err instanceof DOMException && err.name === "AbortError") {
            return;
          } else {
            this.isLoading = false;
          }
        });
    },
    onCancelRequest(id: number) {
      this.isLoading = true;
      this.cancel({
        token: this.userToken,
        id: id,
      })
        .then((res: any) => res.json())
        .then((data: { status: string }) => {
          if (data.status == "success") {
            this.getRequests();
          }
        });
    },
    onClickPage(index: number) {
      this.paginate.currentPage = index;
    },
    onGoToFirstPage() {
      this.paginate.currentPage = 0;
    },
    onGoToPreviousPage() {
      this.paginate.currentPage = this.paginate.currentPage - 1;
    },
    onGoToNextPage() {
      this.paginate.currentPage = this.paginate.currentPage + 1;
    },
    onGoToLastPage() {
      this.paginate.currentPage = this.paginate.totalPages - 1;
    },
  },
  watch: {
    paginate: {
      handler(n, o) {
        if (n.currentPage != o.currentPage) this.getRequests();
      },
      deep: true,
    },
    filter: {
      handler() {
        this.getRequests();
      },
      deep: true,
    },
  },
  mounted() {
    this.getRequests();
  },
});
</script>

<style lang="scss" scoped>
.main {
  width: 100%;
  display: flex;
  flex-direction: column;
  .btn {
    padding: 5px 10px;
    background: transparent;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    margin: 5px 2px;
    &.create {
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
    &.show {
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
    &.cancel {
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
  .heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
  }
  .filter {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10px;
    gap: 10px;
    margin-bottom: 10px;
    &-item {
      display: flex;
      gap: 10px;
      align-items: center;
      width: 100%;
      & > input,
      & > select {
        flex: 1;
        padding: 10px;
        outline: none;
        border: 1px solid var(--color-primary);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        font-size: 0.9rem;
        font-weight: 500;
      }
    }
  }
  table {
    width: 100%;
    thead {
      tr {
        td {
          padding: 10px;
          font-weight: 900;
          font-size: 1rem;
          background: var(--background-glass-color-primary);
          text-align: center;
        }
      }
    }
    tbody {
      tr {
        td {
          padding: 10px;
          font-size: 1rem;
          background: var(--background-glass-color-secondary);
          text-align: center;
          border-bottom: 1px solid var(--background-glass-color-primary);
          height: 40px;
          &.title {
            max-width: 70ch;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            justify-content: center;
          }
          &.control {
            display: flex;
            justify-content: end;
            align-items: center;
            gap: 10px;
          }
        }
      }
    }
  }
}
</style>
