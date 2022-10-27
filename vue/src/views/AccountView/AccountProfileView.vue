<template>
  <teleport to="body">
    <BaseFlatDialog
      :open="dialogWaring.show"
      :title="dialogWaring.title"
      :mode="dialogWaring.mode"
      @close="closeDialog"
    >
      <template #default>
        <p>{{ dialogWaring.content }}</p>
      </template>
    </BaseFlatDialog>
  </teleport>
  <div class="container">
    <h2>Edit Profile</h2>
    <form class="profile" @submit.prevent="onSubmit">
      <div class="profile__row">
        <label for="">Email</label>
        <input type="text" placeholder="Email" v-model="email" />
      </div>
      <div class="profile__row">
        <label for="">User name</label>
        <input type="text" placeholder="Username" v-model="username" />
      </div>
      <div class="profile__row">
        <label for="">Gender</label>
        <select name="gender" id="gender" v-model="gender">
          <option disabled :selected="gender == '' ? true : false" value="">
            Select Gender
          </option>
          <option :selected="gender === 'Male' ? true : false" value="Male">
            Male
          </option>
          <option :selected="gender === 'Female' ? true : false" value="Female">
            Female
          </option>
          <option :selected="gender === 'Unset' ? true : false" value="Unset">
            Don't want to show
          </option>
          <option
            :selected="gender === 'Neutral' ? true : false"
            value="Neutral"
          >
            Neutral Gender
          </option>
          <option :selected="gender === 'Other' ? true : false" value="Other">
            Other
          </option>
        </select>
      </div>
      <div class="profile__row">
        <label for="">Date of Birth</label>
        <div class="profile__row--dob">
          <input
            type="number"
            placeholder="day"
            v-model="dob_date"
            :disabled="isUpadateDisabled"
          />
          <select
            name="month__dob"
            id="month__dob"
            :disabled="isUpadateDisabled"
            v-model="dob_month"
          >
            <option value="-1" disabled>Select Month</option>
            <option
              v-for="(month, index) in monthArray"
              :key="month"
              :value="index + 1"
            >
              {{ month }}
            </option>
          </select>
          <input
            type="number"
            placeholder="year"
            v-model="dob_year"
            :disabled="isUpadateDisabled"
          />
        </div>
      </div>
      <div class="profile__row">
        <label for="">Region</label>
        <select name="region" id="region" v-model="region">
          <option disabled :selected="region == '' ? true : false" value="">
            Select Region
          </option>
          <option
            v-for="country in countryArray"
            :key="country"
            :value="country"
            :selected="region === country ? true : false"
          >
            {{ country }}
          </option>
        </select>
      </div>
      <div class="profile__row">
        <button type="submit">Save Profile</button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseFlatDialog from "../../components/UI/BaseFlatDialog.vue";

export default defineComponent({
  setup() {
    return {};
  },
  data() {
    return {
      countryArray: [
        "Afghanistan",
        "Albania",
        "Algeria",
        "Andorra",
        "Angola",
        "Anguilla",
        "Antigua &amp; Barbuda",
        "Argentina",
        "Armenia",
        "Aruba",
        "Australia",
        "Austria",
        "Azerbaijan",
        "Bahamas",
        "Bahrain",
        "Bangladesh",
        "Barbados",
        "Belarus",
        "Belgium",
        "Belize",
        "Benin",
        "Bermuda",
        "Bhutan",
        "Bolivia",
        "Bosnia &amp; Herzegovina",
        "Botswana",
        "Brazil",
        "British Virgin Islands",
        "Brunei",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Cambodia",
        "Cameroon",
        "Cape Verde",
        "Cayman Islands",
        "Chad",
        "Chile",
        "China",
        "Colombia",
        "Congo",
        "Cook Islands",
        "Costa Rica",
        "Cote D Ivoire",
        "Croatia",
        "Cruise Ship",
        "Cuba",
        "Cyprus",
        "Czech Republic",
        "Denmark",
        "Djibouti",
        "Dominica",
        "Dominican Republic",
        "Ecuador",
        "Egypt",
        "El Salvador",
        "Equatorial Guinea",
        "Estonia",
        "Ethiopia",
        "Falkland Islands",
        "Faroe Islands",
        "Fiji",
        "Finland",
        "France",
        "French Polynesia",
        "French West Indies",
        "Gabon",
        "Gambia",
        "Georgia",
        "Germany",
        "Ghana",
        "Gibraltar",
        "Greece",
        "Greenland",
        "Grenada",
        "Guam",
        "Guatemala",
        "Guernsey",
        "Guinea",
        "Guinea Bissau",
        "Guyana",
        "Haiti",
        "Honduras",
        "Hong Kong",
        "Hungary",
        "Iceland",
        "India",
        "Indonesia",
        "Iran",
        "Iraq",
        "Ireland",
        "Isle of Man",
        "Israel",
        "Italy",
        "Jamaica",
        "Japan",
        "Jersey",
        "Jordan",
        "Kazakhstan",
        "Kenya",
        "Kuwait",
        "Kyrgyz Republic",
        "Laos",
        "Latvia",
        "Lebanon",
        "Lesotho",
        "Liberia",
        "Libya",
        "Liechtenstein",
        "Lithuania",
        "Luxembourg",
        "Macau",
        "Macedonia",
        "Madagascar",
        "Malawi",
        "Malaysia",
        "Maldives",
        "Mali",
        "Malta",
        "Mauritania",
        "Mauritius",
        "Mexico",
        "Moldova",
        "Monaco",
        "Mongolia",
        "Montenegro",
        "Montserrat",
        "Morocco",
        "Mozambique",
        "Namibia",
        "Nepal",
        "Netherlands",
        "Netherlands Antilles",
        "New Caledonia",
        "New Zealand",
        "Nicaragua",
        "Niger",
        "Nigeria",
        "Norway",
        "Oman",
        "Pakistan",
        "Palestine",
        "Panama",
        "Papua New Guinea",
        "Paraguay",
        "Peru",
        "Philippines",
        "Poland",
        "Portugal",
        "Puerto Rico",
        "Qatar",
        "Reunion",
        "Romania",
        "Russia",
        "Rwanda",
        "Saint Pierre &amp; Miquelon",
        "Samoa",
        "San Marino",
        "Satellite",
        "Saudi Arabia",
        "Senegal",
        "Serbia",
        "Seychelles",
        "Sierra Leone",
        "Singapore",
        "Slovakia",
        "Slovenia",
        "South Africa",
        "South Korea",
        "Spain",
        "Sri Lanka",
        "St Kitts &amp; Nevis",
        "St Lucia",
        "St Vincent",
        "St. Lucia",
        "Sudan",
        "Suriname",
        "Swaziland",
        "Sweden",
        "Switzerland",
        "Syria",
        "Taiwan",
        "Tajikistan",
        "Tanzania",
        "Thailand",
        "Timor L'Este",
        "Togo",
        "Tonga",
        "Trinidad &amp; Tobago",
        "Tunisia",
        "Turkey",
        "Turkmenistan",
        "Turks &amp; Caicos",
        "Uganda",
        "Ukraine",
        "United Arab Emirates",
        "United Kingdom",
        "Uruguay",
        "Uzbekistan",
        "Venezuela",
        "Vietnam",
        "Virgin Islands (US)",
        "Yemen",
        "Zambia",
        "Zimbabwe",
      ],
      monthArray: [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ],
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      isUpadateDisabled: false,
      email: "",
      username: "",
      gender: "",
      dob_date: -1,
      dob_month: -1,
      dob_year: -1,
      region: "",
    };
  },
  methods: {
    ...mapActions("auth", ["checkAuth"]),
    ...mapActions("account", ["updateAccount"]),
    onSubmit() {
      if (
        !this.isValidDate(this.dob_date, this.dob_month, this.dob_year) &&
        !this.isUpadateDisabled
      ) {
        this.dialogWaring.content = "Please enter a valid date of birth";
        this.dialogWaring.show = true;
        return;
      }
      this.updateAccount({
        account: {
          email: this.email,
          username: this.username,
          gender: this.gender,
          dob: `${this.dob_month}/${this.dob_date}/${this.dob_year}`,
          region: this.region,
          isUpadateDisabled: this.isUpadateDisabled,
        },
        token: this.token,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.message) {
            this.dialogWaring.content = res.message;
            this.dialogWaring.show = true;
          } else {
            this.$router.push({ name: "accountOverview" });
          }
        });
    },
    isValidDate(day: number, month: number, year: number): boolean {
      // Check the ranges of month and year
      if (year < 1000 || year > 3000 || month == 0 || month > 12) return false;
      var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
      // Adjust for leap years
      if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;
      // Check the range of the day
      return day > 0 && day <= monthLength[month - 1];
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.checkAuth()
      .then((res) => res.json())
      .then((res) => {
        if (res.dob) {
          let date = new Date(res.dob);
          this.dob_date = date.getDate();
          this.dob_month = date.getMonth() + 1;
          this.dob_year = date.getFullYear();
          this.isUpadateDisabled = true;
        }
        this.email = res.email;
        this.username = res.name;
        this.gender = res.gender == "" ? "" : res.gender;
        this.region = res.region == "" ? "" : res.region;
      });
  },
  components: { BaseFlatDialog },
});
</script>

<style lang="scss" scoped>
.container {
  width: 90%;
  max-height: 100%;
  display: flex;
  flex-direction: column;
  overflow: scroll;
  padding: 20px;
  h2 {
    margin-bottom: 20px;
    font-weight: 900;
    font-size: 2rem;
  }
  .profile {
    display: flex;
    flex-direction: column;
    &__row {
      width: 100%;
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
      label {
        font-size: 1rem;
        font-weight: 900;
        margin-bottom: 5px;
      }
      input {
        height: 40px;
        border: 1px solid var(--text-primary-color);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        padding: 0 10px;
        font-size: 1rem;
        &:focus {
          outline: none;
        }
      }
      select {
        height: 40px;
        border: 1px solid var(--text-primary-color);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        padding: 0 10px;
        font-size: 1rem;
        &:focus {
          outline: none;
        }
      }
      &--dob {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        & input {
          width: 28%;
        }
        select {
          width: 30%;
          height: 42px;
        }
      }
      & button {
        height: 40px;
        background: var(--color-primary);
        color: #fff;
        padding: 0 20px;
        font-size: 1rem;
        border-radius: 20px;
        border: none;
        width: max-content;
        float: right;
        cursor: pointer;
        &:focus {
          outline: none;
        }
        &:hover {
          transform: scale(1.05);
          font-weight: 500;
        }
      }
    }
  }
}
</style>
