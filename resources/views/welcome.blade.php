<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>

  <link rel="stylesheet" href="{{ ('css/style.css') }}">

  <title>URL Preview Form</title>

</head>
<body>
  
  <section class="gradient text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto">
      <div class="flex flex-col text-center w-full mb-12">
        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900 ml3">Guerra Assessment</h1>
        <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Enter a URL to preview its content.</p>
      </div>
      <div class="flex lg:w-2/3 w-full sm:flex-row flex-col mx-auto px-8 sm:space-x-4 sm:space-y-0 space-y-4 sm:px-0 items-end">
        <div class="relative flex-grow w-full">
          <label for="url-input" class="leading-7 text-sm text-gray-600"></label>
          <input type="url" id="url-input" name="url-input" placeholder="Input URL here" class="box w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-transparent focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
        </div>
        <button id="preview-button" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg boxs transition">Query</button>
      </div>
      <div class="mt-12 split-container">
        <div class="content-box">
          <h2 class="text-lg font-semibold mb-3">URL Response</h2>
          <div id="preview-content-left"></div>
        </div>
        <div class="content-box">
          <h2 class="text-lg font-semibold mb-3">Processed URL Response</h2>
          <div id="preview-content-right"></div>
        </div>
      </div>
    </div>
  </section>

<!-- ... (Your existing HTML code) ... -->

<script>
  const previewButton = document.getElementById("preview-button");
  const urlInput = document.getElementById("url-input");
  const previewContentLeft = document.getElementById("preview-content-left");
  const previewContentRight = document.getElementById("preview-content-right");

  const countItemsPerLayer = (obj, depth = 0) => {
    const itemCounts = {};
      
    if (typeof obj === "object" && obj !== null) {
      itemCounts[depth] = Object.keys(obj).length;
        
      for (const key in obj) {
        if (typeof obj[key] === "object" && obj[key] !== null) {
          const nestedCounts = countItemsPerLayer(obj[key], depth + 1);
          Object.assign(itemCounts, nestedCounts);
        }
      }
    }
      
    return itemCounts;
  };

  const sortCharsDescending = str => {
    return str.split("").sort((a, b) => b.localeCompare(a)).join("");
  };

  const processJSONContent = obj => {
    const processedContent = {};

    for (const key in obj) {
      if (obj.hasOwnProperty(key)) {
        if (typeof obj[key] === "string") {
          processedContent[key] = sortCharsDescending(obj[key]);
        } else if (typeof obj[key] === "object" && obj[key] !== null) {
          processedContent[key] = processJSONContent(obj[key]);
        } else {
          processedContent[key] = obj[key];
        }
      }
    }

    return processedContent;
  };

  previewButton.addEventListener("click", async () => {
    const url = urlInput.value;

    try {
      const response = await fetch(url);
      const jsonContent = await response.json(); // Assuming the content is in JSON format

      // Display raw JSON response in the left container
      previewContentLeft.innerHTML = `<pre>${JSON.stringify(jsonContent, null, 2)}</pre>`;
        
      // Count items per nesting layer
      const itemCounts = countItemsPerLayer(jsonContent);

      // Display item counts in the right container
      previewContentRight.innerHTML = `
        <h2 class="text-lg font-semibold mb-3">Item Counts per Nesting Layer</h2>
        <pre>${JSON.stringify(itemCounts, null, 2)}</pre>
      `;

      // Process and display specific information
      const objectCount = itemCounts[1];
      const objectNames = Object.keys(jsonContent);
      const keySortedList = sortCharsDescending(objectNames.join(""));
      const processedContent = processJSONContent(jsonContent);

      const previewInfo = `
        <h2 class="text-lg font-semibold mb-3">Processed Information</h2>
        <pre>
          Object count: ${objectCount}
          Descending-sort sequence of all characters of every key string: ${keySortedList}
          Descending-sort sequence in of all characters of every value string: ${JSON.stringify(processedContent, null, 2)}
        </pre>
      `;

      previewContentRight.innerHTML += previewInfo;
    } catch (error) {
      console.error("Error fetching and displaying content:", error);
      previewContentLeft.innerHTML = "Error fetching and displaying content.";
      previewContentRight.innerHTML = "Error fetching and displaying content.";
    }
  });
</script>

<!-- ... (The rest of your HTML code) ... -->


<script src="{{ asset('js/style.js') }}"></script>


</body>
</html>
