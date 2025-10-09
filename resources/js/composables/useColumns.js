// ./src/composables/useColumns.js

// /**
//  * Loads and parses a columns.json file
//  * @param {string} path - relative path from /src/Pages, e.g. 'Users/columns.json'
//  * @returns {object} { columns }
//  */
// export async function useColumns(path) {
//   // import dynamic
//   const columnsJson = await import(`@/Pages/${path}`, {
//     assert: { type: "json" }
//   }).then(m => m.default)

//   const columns = columnsJson.map(col => {
//     if (
//       typeof col.field === 'string' &&
//       (col.field.startsWith('function') || col.field.startsWith('('))
//     ) {
//       return {
//         ...col,
//         // تبدیل رشته به تابع
//         field: eval(`(${col.field})`)
//       }
//     }
//     return col
//   })

//   return { columns }
// }



import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useColumns() {
  const page = usePage();
  
  // محاسبه نام ماژول به‌صورت داینامیک
  const moduleName = computed(() => {
    if (!page.props.theRoute?.parts?.[1]) {
      console.error('theRoute.parts[1] is undefined');
      return null;
    }
    return `${page.props.theRoute.parts[1][0].toUpperCase()}${page.props.theRoute.parts[1].slice(1)}`;
  });

  // متغیر برای ذخیره columns
  const columns = ref(null);

  // لود داینامیک columns در onMounted
  onMounted(async () => {
    if (moduleName.value) {
      try {
        const module = await import(`./${moduleName.value}/columns.js`);
        columns.value = module.default;
      } catch (error) {
        console.error(`Failed to load columns.js for ${moduleName.value}:`, error);
        columns.value = null;
      }
    }
  });

  return { columns, moduleName };
}
// import { useColumns } from '@/composables/useColumns';
// const { columns } = useColumns();
